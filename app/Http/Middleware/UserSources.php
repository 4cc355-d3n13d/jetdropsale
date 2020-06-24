<?php

namespace App\Http\Middleware;

use App\Models\User\UserSource;
use Closure;
use Illuminate\Http\Response;
use UserAgent;
use Webpatser\Uuid\Uuid;

class UserSources
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        $host = transform(parse_url($request->headers->get('referer')), function ($parsed) {
            return $parsed['host'] ?? null;
        });
        if (auth()->guest() && ! strpos(env('APP_URL'), $host) &&
            ($request->cookies->has(UserSource::DROPWOW_UUID_COOKIE) || ! UserAgent::isRobot())
        ) {
            $dropwowUuid = $this->setUserSources($request);

            $response = $response->withCookie(cookie()->forever(UserSource::DROPWOW_UUID_COOKIE, $dropwowUuid));
        }

        return $response;
    }

    /**
     * @param  \Illuminate\Http\Request $request
     */
    private function setUserSources($request): string
    {
        $dropwowUuid = $request->cookies->get(UserSource::DROPWOW_UUID_COOKIE) ?? (string) Uuid::generate();

        UserSource::create([
            'path' => $request->path(),
            'cookie_hash' => $dropwowUuid,
            'http_referrer_domain' => transform(parse_url($request->headers->get('referer')), function ($parsed) {
                return $parsed['host'] ?? null;
            }),
            'http_referrer_full' => $request->header('referer') ? substr($request->header('referer'), 0, 255) : null,
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_content' =>$request->get('utm_content'),
            'utm_term' => $request->get('utm_term'),
            'ip' => $request->ip(),
            'full_url' => $request->fullUrl(),
        ]);

        return $dropwowUuid;
    }
}
