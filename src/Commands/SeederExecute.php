<?php

namespace Flixmedia\SuperSeeder\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederExecute extends Command
{
    protected $signature = 'super_seeder:seed {--class=} {--only_lastest_months=}';

    protected $description = 'Execute flix seeder';

    public function handle()
    {

        DB::transaction(function () {
            $seeder_classes = $this->getSeederClasses();

            foreach ($seeder_classes as $seeder_class) {
                if ($this->isAlreadyExecuted($seeder_class)) {
                    $this->error("Running seeder: $seeder_class");
                    continue;
                }
                $this->info("Executing seeder: $seeder_class");
                if (!class_exists($seeder_class)) {
                    $this->error("CLass $seeder_class not exists");
                    continue;
                }

                $seeder_instance = app($seeder_class);

                if (!($seeder_instance instanceof Seeder)) {
                    $this->error("CLass $seeder_class is not instance of Seeder");
                    continue;
                }

                $seeder_instance->run();
                $this->markAsExecuted($seeder_class);
            }
        });
    }

    private function getSeederClasses(): array
    {
        $class_name = $this->option('class');

        if (!$class_name) {
            return $this->getNewFlixSeederClasses();
        }

        return [
            "Database\\Seeders\\$class_name"
        ];
    }


    function getNewFlixSeederClasses(): array
    {
        $new_seeders = [];
        $seeder_dir  = realpath(__DIR__  . '/../../../database/seeders/');
        foreach (scandir($seeder_dir) as $folder_name) {

            $is_flix_seeder_dir = is_dir($seeder_dir . DIRECTORY_SEPARATOR . $folder_name) && str_starts_with($folder_name, 'FlixSeeder_');

            if(!$is_flix_seeder_dir || $this->isOldSeederFolder($folder_name)) {
                continue;
            }

            foreach (scandir($seeder_dir .DIRECTORY_SEPARATOR . $folder_name) as $class_file_name) {
                if($class_file_name[0] === '.') continue;
                list($seeder_class) = explode('.', $class_file_name);
                $seeder_class = "Database\\Seeders\\$folder_name\\$seeder_class";
                if ($this->isAlreadyExecuted($seeder_class)) {
                    continue;
                }
                $new_seeders[$folder_name . DIRECTORY_SEPARATOR . $class_file_name] = $seeder_class;
            }
        }

        return $new_seeders;
    }


    public function isOldSeederFolder($folder_name): bool
    {
        $only_lastest_months = (int) $this->option('only_lastest_months');

        if($only_lastest_months) {
            $max_old_folder = config('super_seeder.seeder_folder_prefix') . now()->subMonths($only_lastest_months)->format('Ym');
            return strcmp($max_old_folder, $folder_name) > 0;
        }

        return false;
    }


    private function isAlreadyExecuted($class_name): string
    {
        return DB::table(config('super_seeder.table_name'))
            ->where('seeder_name', $class_name)
            ->exists();
    }

    private function markAsExecuted($seeder_name): void
    {
        DB::table(config('super_seeder.table_name'))
            ->insert([
                'seeder_name' => $seeder_name,
                'executed_at' => now(),
            ]);
    }
}
