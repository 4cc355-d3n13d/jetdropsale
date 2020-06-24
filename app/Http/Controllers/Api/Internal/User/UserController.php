<?php

namespace App\Http\Controllers\Api\Internal\User;

use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Resources\Collection;
use App\Http\Resources\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends ApiController
{
    // the only api route here...
    public function list(?string $beginning = null)
    {
        $users = $beginning ? User::where('email', 'like', $beginning . '%')->get() : User::all();

        return new Collection($users, 'user');
    }

    public function whoami()
    {
        return new Item(auth()->user(), 'user');
    }

    public function impersonate(User $user)
    {
        Auth::user()->setImpersonating($user->id);

        return redirect(action([self::class, 'isImpersonate']));
    }

    public function stopImpersonate()
    {
        Auth::user() && Auth::user()->stopImpersonating();

        return redirect()->back();
    }

    public function isImpersonate()
    {
        if (Auth::user() && Auth::user()->isImpersonating()) {
            return redirect('/');
        } else {
            return 'false';
        }
    }
}
