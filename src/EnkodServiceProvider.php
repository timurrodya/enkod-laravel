<?php

namespace Timurrodya\Enkod;

use Illuminate\Support\ServiceProvider;

class EnkodServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // php artisan vendor:publish --provider='Timurrodya\Enkod\EnkodServiceProvider' --tag=config
        $this->publishes([
            __DIR__ . '/../config/enkod.php' => config_path('enkod.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/enkod.php', 'enkod');

        $this->app->bind('enkod', function () {
            return new Enkod;
        });
    }
}
