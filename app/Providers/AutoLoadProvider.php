<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AutoLoadProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path().'/AutoLoad/*.php') as $filename) {
            require_once($filename);
        }
    }
}
