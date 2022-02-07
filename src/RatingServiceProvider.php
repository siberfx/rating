<?php

namespace Rennokki\Rating;

use Illuminate\Support\ServiceProvider;

class RatingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/rating.php' => config_path('rating.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
