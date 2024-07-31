<?php

namespace Internetguru\SmartRedirect;

use Illuminate\Support\ServiceProvider;

class SmartRedirectServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/smart-redirect.php' => config_path('smart-redirect.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/smart-redirect.php',
            'smart-redirect'
        );
    }

    public function register()
    {
        // Register middleware
        $this->app['router']->aliasMiddleware('redirect', \Internetguru\SmartRedirect\Middleware\Redirect::class);
    }
}
