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
        // Check whether the current user is a developer based on IP address
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
        // Enable Eloquent "strict" mode (detects lazy loading, attribute / relation typos,
        // mass assignment violations, etc.) for every environment except production.
        // This helps surface bugs early during local development & testing without
        // incurring the (small) runtime overhead in production.
        \Illuminate\Database\Eloquent\Model::shouldBeStrict(!in_array(app()->environment(), ['prod', 'production']));
    }
}
