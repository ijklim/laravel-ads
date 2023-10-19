<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Make available `$this->app->isDeveloper`
        // Check whether the current user is a developer based on ip address
        $this->app->isDeveloper = in_array(
            request()->ip(),
            [
                config('app.ip_developer'),
                '127.0.0.1',
            ]
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
