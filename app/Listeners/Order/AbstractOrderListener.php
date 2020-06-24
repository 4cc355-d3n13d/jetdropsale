<?php

namespace App\Listeners\Order;

use Illuminate\Support\Facades\Log;

abstract class AbstractOrderListener
{
    /**
     * @param string|array $message
     */
    protected function log($message, array $context = [], string $level = 'info', string $channel = 'billing'): void
    {
        Log::channel($channel)->$level(
            is_array($message)
                ? call_user_func_array('sprintf', $message)
                : (string) $message,
            $context
        );
    }
}
