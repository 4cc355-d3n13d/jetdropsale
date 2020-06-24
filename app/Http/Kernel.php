<?php

namespace App\Http;

use App\Http\Middleware\SentryContext;
use App\Http\Middleware\UserSources;
use Illuminate\Auth;
use Illuminate\Cookie;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware as FoundationMiddleware;
use Illuminate\Http;
use Illuminate\Routing;
use Illuminate\Session;
use Illuminate\View;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        FoundationMiddleware\CheckForMaintenanceMode::class,
        FoundationMiddleware\ValidatePostSize::class,
        Middleware\TrimStrings::class,
        FoundationMiddleware\ConvertEmptyStringsToNull::class,
        Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            Middleware\EncryptCookies::class,
            Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Session\Middleware\StartSession::class,
            // Session\Middleware\AuthenticateSession::class,
            View\Middleware\ShareErrorsFromSession::class,
            Middleware\VerifyCsrfToken::class,
            'impersonate',
            'bindings',
            SentryContext::class,
            UserSources::class,
        ],

        'api.internal' => [
            Middleware\EncryptCookies::class, // required for auth
            Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Session\Middleware\StartSession::class, // required for auth
            Middleware\VerifyCsrfToken::class, // recommended for security
            Middleware\ApiAuth::class, // only for auth user, not for guest
            View\Middleware\ShareErrorsFromSession::class,
            'impersonate',
            'bindings',
            SentryContext::class
        ],

        'api.external' => [
            //'throttle:60,1',
            'bindings',
            SentryContext::class
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     */
    protected $routeMiddleware = [
        'auth' => Auth\Middleware\Authenticate::class,
        'auth.basic' => Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => Http\Middleware\SetCacheHeaders::class,
        'can' => Auth\Middleware\Authorize::class,
        'guest' => Middleware\RedirectIfAuthenticated::class,
        'signed' => Routing\Middleware\ValidateSignature::class,
        'throttle' => Routing\Middleware\ThrottleRequests::class,
        'charged' => Middleware\ActiveChargeCheck::class,
        'impersonate' => Middleware\Impersonate::class,
    ];
}
