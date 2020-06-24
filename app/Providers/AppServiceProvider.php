<?php

namespace App\Providers;

use App\Enums\MyProductStatusType;
use App\Http\Middleware\ShopifyLog;
use App\Models\Setting;
use App\SuperClass\Facades\Omnipay;
use App\SuperClass\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;
use Omnipay\Common\GatewayInterface;

/**
 * Class AppServiceProvider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_MONETARY, config('app.money.format'));
        app()->instance(GatewayInterface::class, Omnipay::gateway());
        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function (ViewContract $view) {
            if (auth()->check()) {
                $view->with("myProductNonConnectedCount", cache()->remember('non-connected-count.' . auth()->id(), 60, function () {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return auth()->user()->myProducts()->whereStatus(MyProductStatusType::NON_CONNECTED)->count();
                }));

                $view->with("myProductConnectedCount", cache()->remember('connected-count.' . auth()->id(), 60, function () {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return auth()->user()->myProducts()->whereStatus(MyProductStatusType::CONNECTED)->count();
                }));

                $view->with('user', auth()->user());
            }
        });

        try {
            // we do not need that at the build time
            DB::connection()->getPdo();
            if (Schema::hasTable('settings')) {
                foreach (Setting::all() as $setting) {
                    config()->set('settings.' . $setting->key, $setting->value);
                }
            }
        } catch (\Exception $e) {
        }

        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('ShopifyLog', function ($app) {
            /** @var App $app */
            return $app->make(ShopifyLog::class);
        });
    }
}
