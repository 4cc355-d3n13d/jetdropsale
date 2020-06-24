<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        parent::boot();
        $this->pattern('id', '[0-9]+');
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        if (app()->isLocal() && (request()->getHost() === env('API_HOST'))) {
            config(['app.is_api' => true]);
        }
        if (app()->runningUnitTests()) {
            $this->mapExternalApiRoutes('test');
        }
        if (config('app.is_api', false)) {
            $this->mapExternalApiRoutes();
        } else {
            $this->mapInternalApiRoutes();
            $this->mapWebRoutes();
        }
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'))
        ;
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     */
    protected function mapInternalApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware('api.internal')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.internal.php'))
        ;
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     * @param null $prefix
     */
    protected function mapExternalApiRoutes($prefix = null): void
    {
        Route::prefix($prefix)->middleware('api.external')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.external.php'))
        ;
    }
}
