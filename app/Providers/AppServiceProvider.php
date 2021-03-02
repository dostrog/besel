<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # ensure you configure the right channel you use
        config(['logging.channels.besel.path' => \Phar::running()
            ? dirname(\Phar::running(false)) . '/logs/besel.log'
            : getcwd() . '/logs/besel.log'
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
