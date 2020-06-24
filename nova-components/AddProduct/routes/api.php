<?php

use App\Models\Product\Product;
use App\Services\AliProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::get('/ali', function (Request $request) {
    if (! $aliId = $request->get('id')) {
        App::abort(Response::HTTP_BAD_REQUEST, 'Do not forget to send a payload (id)');
    }

    if ($product = Product::where('ali_id', $aliId)->first()) {
        return [
            'result' => 'ok',
            'redirect' => "/nova/resources/products/{$product->id}"
        ];
    }

    // do work
    App::make(AliProductService::class)->fetchById($aliId);

    return [
        'result' => 'ok',
        'message' => "Job for adding product #{$aliId} was just sent to the queue."
    ];
});
