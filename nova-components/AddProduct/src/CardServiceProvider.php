<?php

namespace Dropwow\AddProduct;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('add_product', __DIR__.'/../dist/js/card.js');
            Nova::style('add_product', __DIR__.'/../dist/css/card.css');
        });
    }

    /**
     * Register the card's routes.
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route
            ::middleware(['nova'])
            ->prefix('nova-dropwow/add-product')
            ->group(__DIR__.'/../routes/api.php')
        ;
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
