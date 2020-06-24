<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;

/**
 * Class ShopifyLog
 */
class ShopifyLog
{
    protected $start;
    protected $end;
    protected $content;
    protected $needSaveRequest = true;

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (class_basename($request->route()->controller) == 'StubController') {
            $this->needSaveRequest = false;
        }
        $this->start = microtime(true);
        $response = $next($request);
        $this->content  = $response->getContent();
        return $response;
    }

    /**
     * @param Request $request
     */
    public function terminate($request): void
    {
        $this->end = microtime(true);

        $this->log($request);
    }

    /**
     * @param Request $request
     */
    protected function log($request): void
    {
        if (!$this->needSaveRequest) {
            return;
        }
        $duration = round($this->end - $this->start, 4);
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();
        $shop = $request->header("x-shopify-shop-domain");
        $apiMethod = parse_url($url)['path'];

        $log = "[$ip]:[$method@$url]:[$shop] [{$this->content}] {$duration}ms";
        if (app()->environment(['local', 'testing'])) {
            $logChannel = Log::channel('daily');
        } else {
            $logChannel = Log::channel('shopify');
        }

        $logChannel->info($log, [
            'Method' => $apiMethod,
            'Answer' => $this->content,
            'Headers' => $request->headers->all(),
            'RawBody' => $request->getContent(),

        ]);
    }
}
