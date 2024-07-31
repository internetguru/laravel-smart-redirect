# Laravel Smart Redirect

A configurable Laravel 11 middleware package that handles URL redirection based on defined routes and uses Levenshtein distance to find the closest matching route for 404 errors.

## Features

- Redirect URLs based on predefined rules
- Automatically find the closest matching route using Levenshtein distance
- Easily configurable parameters for route combinations

## Installation

1. Require the package via Composer:

    ```bash
    composer require internetguru/laravel-smart-redirect
    ```

2. Publish the configuration file:

    ```bash
    php artisan vendor:publish --provider="Internetguru\\SmartRedirect\\SmartRedirectServiceProvider" --tag="config"
    ```

3. Register the service provider (if not auto-discovered) in `config/app.php`:

    ```php
    'providers' => [
        // Other Service Providers
        Internetguru\SmartRedirect\SmartRedirectServiceProvider::class,
    ],
    ```

## Configuration

After publishing the configuration file, you can configure your redirects and parameters in `config/smart-redirect.php`.

```php
return [
    'redirects' => [
        // '/old-path' => '/new-path',
    ],
    'params' => [
        // 'locale' => ['en', 'cs'],
        // 'location' => ['racineves', 'kralupy'],
    ],
];

## Usage

To always use the middleware, add it to `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // Other Middleware
        \Internetguru\SmartRedirect\Middleware\SmartRedirectMiddleware::class,
    ],
];
```

You can also use the middleware only for some of the routes. Register the middleware in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    'smart-redirect' => \Internetguru\SmartRedirect\Middleware\SmartRedirectMiddleware::class,
];
```
And then use it in your routes, e.g. in `routes/web.php`:

```php
Route::get('/old-path', function () {
    return 'This is the old path.';
})->middleware('smart-redirect');
```

## Example

Let's say you have a website with the following routes:

- `/`
- `/about`
- `/contact`
- `/services`
- `/services/web-development`
- `/services/mobile-development`
- `/services/seo`

And you want to redirect the following URLs:

- `/web-dev` to `/services/web-development`
- `/mobile-dev` to `/services/mobile-development`
- `/seo` to `/services/seo`

You can define the redirects in `config/smart-redirect.php`:

```php
'redirects' => [
    '/web-dev' => '/services/web-development',
    '/mobile-dev' => '/services/mobile-development',
    '/seo' => '/services/seo',
],
```

Now, when you visit `/web-dev`, you will be redirected to `/services/web-development`.

If you visit a non-existing URL, e.g. `/web`, the middleware will automatically find the closest matching route and redirect you to the correct one.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
