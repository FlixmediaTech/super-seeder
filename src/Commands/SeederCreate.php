<?php

namespace Flixmedia\SuperSeeder\Commands;

use Illuminate\Console\Command;

class SeederCreate extends Command
{
    protected $signature = 'super_seeder:create {seeder_name}';

    protected $description = 'Create trackable seeder use add data in database.';

    protected $seeder_folder = null;


    public function handle()
    {
        $this->seeder_folder = 'FlixSeeder_' . now()->format('Y_m');

        $directory =  $this->getNewSeederDirectory();
        $seeder_class_name = $this->generateSeederClassName();
        $content = $this->getSeederClassContent($seeder_class_name);

        $file_path = $directory . DIRECTORY_SEPARATOR . $seeder_class_name . '.php';
        file_put_contents($file_path, $content);

        $this->info("Seeder file created: " . $file_path);

        return 0;
    }


    protected function getNewSeederDirectory(): string
    {
        $directory =  realpath(__DIR__  . '/../../../database/seeders/');
        $directory = $directory . DIRECTORY_SEPARATOR . $this->seeder_folder;

        if(!is_dir($directory)) {
            mkdir($directory);
        }

        return $directory;
    }


    protected function generateSeederClassName(): string
    {
        $seeder_name = preg_replace('/\s+/', '', $this->argument('seeder_name'));
        $time =  now()->format('d_His');

        return sprintf("Seeder_%s_%s",  $time, $seeder_name);
    }


    protected function getSeederClassContent($className)
    {
        return "<?php

namespace Database\\Seeders\\$this->seeder_folder;

use Illuminate\Database\Seeder;

class $className extends Seeder
{
    public function run()
    {
        //Write your code here
    }
   
}";
    }
}
