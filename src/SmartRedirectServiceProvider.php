<?php

namespace Internetguru\SmartRedirect;

use Illuminate\Support\ServiceProvider;

class SmartRedirectServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/smart-redirect.php' => config_path('smart-redirect.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/smart-redirect.php',
            'smart-redirect'
        );
    }

    public function register()
    {
        $this->app['router']->aliasMiddleware('redirect', \Internetguru\SmartRedirect\Middleware\Redirect::class);
    }
}
