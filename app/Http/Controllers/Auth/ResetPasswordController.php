<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Foundation\Auth\ResetsPasswords;

/**
 * Class ResetPasswordController
 */
class ResetPasswordController extends WebController
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     */
    protected $redirectTo = '/catalog';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
