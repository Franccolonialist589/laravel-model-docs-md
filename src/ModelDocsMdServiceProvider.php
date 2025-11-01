<?php

namespace Rakib01\LaravelModelDocsMd;

use Illuminate\Support\ServiceProvider;
use Rakib01\LaravelModelDocsMd\Commands\GenerateModelDocsCommand;

class ModelDocsMdServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateModelDocsCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/modeldocsmd.php' => config_path('modeldocsmd.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/modeldocsmd.php', 'modeldocsmd');
    }
}
