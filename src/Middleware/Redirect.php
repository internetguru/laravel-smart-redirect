<?php

namespace Internetguru\SmartRedirect\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Redirect
{
    public function handle(Request $request, Closure $next)
    {
        $redirects = config('smart-redirect.redirects');

        if (isset($redirects[$request->path()])) {
            return redirect($redirects[$request->path()]);
        }

        $response = $next($request);

        if ($response->getStatusCode() == 404) {
            $currentPath = $request->path();
            $previousUrl = url()->previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);
            $allRoutes = collect(Route::getRoutes())->map(function ($route) {
                return $this->generateRouteCombinations($route->uri());
            })->collapse()->toArray();

            $closestRoute = $this->findClosestRoute($currentPath, $allRoutes);
            if ($closestRoute && $closestRoute !== $previousPath) {
                return redirect($closestRoute);
            }
        }

        return $response;
    }

    protected function generateRouteCombinations($route)
    {
        $params = config('smart-redirect.params');

        $combinations = [''];

        foreach (explode('/', $route) as $part) {
            if (preg_match('/\{([^\/]+)\}/', $part, $matches) && isset($params[$matches[1]])) {
                $newCombinations = [];
                foreach ($combinations as $combination) {
                    foreach ($params[$matches[1]] as $value) {
                        $newCombinations[] = rtrim($combination . '/' . $value, '/');
                    }
                }
                $combinations = $newCombinations;
            } else {
                $combinations = array_map(
                    fn ($combination) => rtrim($combination . '/' . $part, '/'),
                    $combinations
                );
            }
        }

        return $combinations;
    }

    protected function findClosestRoute($currentPath, $allRoutes)
    {
        $closestRoute = null;
        $shortestDistance = -1;

        foreach ($allRoutes as $route) {
            $distance = levenshtein($currentPath, $route);

            if ($distance == 0) {
                $closestRoute = $route;
                $shortestDistance = 0;
                break;
            }

            if ($distance < $shortestDistance || $shortestDistance < 0) {
                $closestRoute = $route;
                $shortestDistance = $distance;
            }
        }

        if ($shortestDistance <= 3) {
            return $closestRoute;
        }

        return null;
    }
}
