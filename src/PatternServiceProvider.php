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
            __DIR__.'/config/jsonapi.php' => base_path('config/jsonapi.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRepo::class,
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
