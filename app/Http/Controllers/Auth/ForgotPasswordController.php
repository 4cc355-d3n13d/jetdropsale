<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

/**
 * Class ForgotPasswordController
 */
class ForgotPasswordController extends WebController
{
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
