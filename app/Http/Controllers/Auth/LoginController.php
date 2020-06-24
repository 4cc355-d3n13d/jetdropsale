<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class LoginController
 */
class LoginController extends WebController
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
