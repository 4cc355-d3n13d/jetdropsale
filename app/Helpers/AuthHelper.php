<?php

namespace App\Helpers;

/**
 * Class AuthHelper
 */
class AuthHelper
{
    private static $encMethod = 'aes-256-ctr';

    public static function decrypt($data, $iv)
    {
        $iv = str_pad(base64_decode($iv), 16, '\0');
        return openssl_decrypt(base64_decode($data), self::$encMethod, env('SHOPIFY_ENC_KEY'), 1, $iv);
    }
}
