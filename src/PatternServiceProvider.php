<?php

namespace g4t\Pattern;

use g4t\Pattern\GenerateRepo;
use Illuminate\Support\ServiceProvider;

class PatternServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config/jsonapi.json' => base_path('config/jsonapi.json'),
        ]);

        $this->publishes([
            __DIR__ . '/Repositories/BaseRepository.php' => base_path('app/Http/Repositories/BaseRepository.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Helpers/Helpers.php' => base_path('app/Http/Repositories/Helpers.php'),
        ]);



        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRepo::class,
                GenerateValidation::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
