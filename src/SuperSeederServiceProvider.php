<?php

namespace Flixmedia\SuperSeeder;

use Flixmedia\SuperSeeder\Commands\SeederCreate;
use Flixmedia\SuperSeeder\Commands\SeederExecute;
use Illuminate\Support\ServiceProvider;


class SuperSeederServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config/super_seeder.php' => config_path('super_seeder.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/config/super_seeder.php', 'super_seeder'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SeederCreate::class,
                SeederExecute::class
            ]);
        }
    }
}
