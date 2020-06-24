<?php

namespace App\Providers;

use App\Nova\Tools\DevTools;
use Laravel\Horizon\Horizon;
use Laravel\Nova\Nova;
use Laravel\Nova\Cards\Help;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\PendingRouteRegistration;
use PragmaRX\ArtisanTool\Tool;
use Sbine\RouteViewer\RouteViewer;
use Spatie\BackupTool\BackupTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
        Horizon::auth(function () {
            return Gate::check("viewNovaDevTools");
        });
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        /** @var PendingRouteRegistration $novaRoutes */
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        Nova::routes()->register();
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     */
    protected function cards(): array
    {
        $cards = [];
        if (Gate::check("viewNovaDevTools")) {
            $cards[] = new \Kreitje\NovaHorizonStats\JobsPastHour(5);
            $cards[] = new \Kreitje\NovaHorizonStats\FailedJobsPastHour(10);
            $cards[] = new \Kreitje\NovaHorizonStats\Processes(15);
            $cards[] = new \Kreitje\NovaHorizonStats\Workload(30);
            $cards[] = (new \PeterBrinck\NovaLaravelNews\NovaLaravelNews())->width('full');
            $cards[] = new Help;
        }

        return $cards;
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     */
    public function tools(): array
    {
        $tools = [];
        if (Gate::check("viewNovaDevTools")) {
            $tools[] = new RouteViewer;
            $tools[] = new DevTools;
        }

        return $tools;
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
