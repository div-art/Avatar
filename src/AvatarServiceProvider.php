<?php

namespace DivArt\Avatar;

use Illuminate\Support\ServiceProvider;

class AvatarServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/avatar.php' => config_path('avatar.php'),
            __DIR__ . '/fonts' => public_path('/fonts'),
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('avatar', function () {
            return new Avatar;
        });
    }
}