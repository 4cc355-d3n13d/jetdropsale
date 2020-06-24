<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class InvoiceController
 */
class InvoiceController
{
    /**
     * @param User $user
     * @return Factory|View
     */
    public function __invoke(User $user)
    {
        // Get the currently authenticated user...
        /** @var User $user */
        $user = Auth::user();
        $cards = $user->cards;

        return view('invoice.index', compact('cards', 'user'));
    }
}
