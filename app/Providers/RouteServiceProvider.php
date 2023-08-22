<?php

namespace App\Providers;

use App\Exceptions\ApplicationErrorException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    public function getRouteGroups(): array
    {
        return [
            ['file' => 'web', 'prefix' => '', 'middleware' => 'web'],

            ['path' => 'api/import', 'middleware' => 'api'],

//            ['path' => 'api/test', 'middleware' => 'api'],
        ];
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::pattern('id', '[0-9]+');

        $this->routes(function () {
            foreach ($this->getRouteGroups() as $routeGroup) {
                if (isset($routeGroup['env']) && !App::environment($routeGroup['env']))
                    continue;

                $path = $routeGroup['path'] ?? '';
                $file = $routeGroup['file'] ?? '';
                $prefix = $routeGroup['prefix'] ?? null;
                $middleware = $routeGroup['middleware'] ?? null;

                if (!empty($file)) {
                    $this->registerRouteGroup($file, $path, $prefix, $middleware);
                } else {
                    foreach (File::files(base_path('routes/' . $path)) as $file) {
                        $this->registerRouteGroup($file->getFilenameWithoutExtension(), $path, $prefix, $middleware);
                    }
                }
            }
        });
    }

    private function registerRouteGroup(string $file, string $path = '', string $prefix = null, array|string $middleware = null)
    {
        if (empty($file))
            throw new ApplicationErrorException('Filename must not be empty');

        $filePath = $path . '/' . $file;
        $prefix = is_null($prefix) ? $path : $prefix;

        $route = Route::prefix($prefix);
        if (!empty($middleware)) {
            $route->middleware($middleware);
        }
        $route->group(base_path("routes/$filePath.php"));
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
