<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Class VerifyCsrfToken
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     */
    //TODO: add csrf-token to api requests and remove this 'except'
    protected $except = [
        '/api/*',
        '/logout' // Doesn`t work @ SPA
    ];
}
