<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Dropwow Web Application Routes
 *
 */


Route::get('/', function () {
    return redirect('catalog');
});

Route::get('/redirect', function () {
    return redirect(request('to', '/'));
});

Route::group(['namespace' => 'Auth'], function () {
    Route::get('login/{provider}', 'SocialLoginController@login')->name('auth.socialite');
    Route::get('login/{provider}/callback', 'SocialLoginController@callback');
});

Route::group(['namespace' => 'Charge'], function () {
    Route::get('charge/check', 'ChargeController@checkChargeAction')->name('checkCharge');
});

Auth::routes();

Route::group(['namespace' => 'Product', 'prefix' => 'product', 'as' => 'product.'], function () {
    Route::get('/_/{id}', function ($id) {
        $product = Product\Product::where(['ali_id'=>$id])->first();
        if ($product) {
            return redirect(route('product.show', ['id'=>$product->id]));
        } else {
            app('\App\Services\AliProductService')->fetchById($id);
            return redirect("/");
        }
    });
    Route::get('/{product}', 'ProductController@show')->name('show');
});

Route::group(['namespace' => 'Product', 'prefix' => 'category', 'as' => 'category.'], function () {
    Route::get('/_/{slug}', function ($slug) {
        $category = Product\Category::where(['slug'=>$slug])->first();
        if ($category) {
            return redirect(route('category.products', ['id'=>$category->id]));
        } else {
            return redirect("/");
        }
    });
    Route::get('/{category}', 'CategoryController@products')->name('products');
});



Route::get('/catalog/', 'Catalog\CatalogController@index')
    ->middleware('charged')
    ->name('catalog');

Route::get('/catalog/search', 'Product\CategoryController@search')->name('catalog.search');

Route::get('/my{path}', function () {
    return view('layouts.spa');
})->where('path', '.*')->middleware('auth');

Route::group(['namespace' => 'Api\Internal', 'prefix' => 'api/products', 'as' => 'product.'], function () {
    Route::get('/{product}/variants', 'ProductController@variants')->name('variants');
});

Route::get('logout', function () {
    auth()->guard()->logout();

    request()->session()->flush();
    request()->session()->regenerate();

    return redirect('/');
});

Route::group(['namespace' => 'Api\Internal\User', 'prefix' => 'user/impersonate', 'as'=>'impersonate.'], function () {
    Route::get('/status', 'UserController@isImpersonate')->name('status');
    Route::get('/stop', 'UserController@stopImpersonate')->name('stop');
    Route::get('/{user}', 'UserController@impersonate')->name('start')->middleware('can:canImpersonate');
});

Route::group(['prefix' => 'data', 'as' => 'data.'], function () {
    Route::group(['prefix'=>'shopify', 'middleware'=>['can:viewShopifyData'], 'as'=>'shopify.'], function () {
        Route::get('/orders', 'ShopifyController@orders')->name('orders');
        Route::get('/order-sync', 'ShopifyController@orderSync')->name('order-sync');
    });

    Route::get('/import-from-cscart', function () {
        $query = \App\Models\Shopify\Shop::query();
        if (request()->has('shop_id')) {
            $query->where(['id' => request('shop_id')]);
        }
        $query->get()->each->syncCsCart();
    })->middleware(['can:isAdmin']);

    Route::get('/shopify-set-hook/{shop}', function (\App\Models\Shopify\Shop $shop) {
        \App\Jobs\Shopify\InitShopifyHooks::dispatchNow($shop);
    })->middleware(['can:isAdmin']);

    Route::group(['prefix' => '_', 'as' => 'invisible'], function () {
        Route::get('/whattimeisnow', function () {
            return time();
        });
    });

    Route::get('/phpinfo', function () {
        phpinfo();
    })->middleware(['can:isAdmin']);
});
