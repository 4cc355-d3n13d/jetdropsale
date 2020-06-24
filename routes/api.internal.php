<?php

use Illuminate\Support\Facades\Route;

/**
 * Dropwow Internal REST API Routes
 */

// /api/user/...
Route::group(['namespace' => 'Api\Internal\User', 'prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/cards', 'CardsController@getCards')->name('cards.get');
    Route::post('/cards', 'CardsController@addCard')->name('card.add');
    Route::delete('/cards/{card_id}', 'CardsController@removeCard')->name('card.remove');
    Route::any('/cards/{card}/primary', 'CardsController@setPrimary')->middleware(['can:update,card'])->name('card.primary');

    Route::get('/invoices', 'InvoicesController@getInvoiceList')->name('invoice.list');
    Route::get('/invoices/{invoice_id}', 'InvoicesController@getDetailedInvoiceData')->name('invoice.detailed');
    Route::put('/invoices/{invoice_id}/payment', 'InvoicesController@requestInvoicePayment')->name('invoice.pay');

    Route::get('/settings', 'SettingsController@getSettings')->name('settings.get');
    Route::put('/settings', 'SettingsController@storeSettings')->name('settings.store');

    Route::get('/orders', 'OrdersController@list')->name('orders.list');
    Route::put('/orders/{order}/status/{new_status}', 'OrdersController@changeStatus')->middleware(['can:update,order'])->name('order.status');
    Route::get('/orders/statuses', 'OrdersController@listStatuses')->name('orders.status');
    Route::put('/orders/mass-status', 'OrdersController@changeStatuses')->name('orders.mass-status');

    Route::get('/whoami', 'UserController@whoami');
    Route::get('/list', 'UserController@list');
});

// /api/users/...
Route::group(['namespace' => 'Api\Internal\User', 'prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/whoami', 'UserController@whoami');
    Route::get('/list/{beginning}', 'UserController@list')->middleware(['can:viewUserList']);
});

// /api/my-products
Route::group(['namespace' => 'Api\Internal', 'prefix' => 'my-products', 'as' => 'my-product.'], function () {
    Route::get('/', 'MyProductController@list')->name('get');
    Route::get('/count', 'MyProductController@count')->name('count');
    Route::delete('/', 'MyProductController@delete')->name('delete');

    Route::group(['prefix' => 'tags', 'as' => 'tag.'], function () {
        Route::get('/', 'MyProduct\TagController@find')->name('find');
        Route::delete('/{id}', 'MyProduct\TagController@delete')->name('delete');
    });

    Route::group(['prefix' => 'collections', 'as' => 'collection.'], function () {
        Route::get('/', 'MyProduct\CollectionController@find')->name('find');
        Route::post('/', 'MyProduct\CollectionController@create')->name('create');
        Route::delete('/{id}', 'MyProduct\CollectionController@delete')->name('delete');
    });

    Route::group(['prefix' => '/{my_product}', 'middleware' => ['can:view,my_product']], function () {
        Route::put('/', 'MyProductController@store')->name('store');
        Route::get('/', 'MyProductController@view')->name('view');
        Route::post('/clone', 'MyProductController@clone')->name('clone');
        Route::post('/split/{option_id}', 'MyProductController@split')->name('split');

        Route::get('/variants', 'MyProductController@getVariants')->name('variants');
        Route::put('/variants/{variant_id}', 'MyProductController@storeVariant')->name('variant.store');
        Route::put('/variants', 'MyProductController@storeVariants')->name('variants.store');
        Route::delete('/variants/{variant_id}', 'MyProductController@deleteVariant')->name('variant.delete');
        Route::delete('/variants', 'MyProductController@deleteVariants')->name('variants.delete');

        Route::get('/images', 'MyProductController@getProductImages')->name('images');
        Route::delete('/images', 'MyProductController@deleteImages')->name('images.delete');
        Route::post('/image', 'MyProductController@storeImage')->name('image.store');
        Route::post('/images', 'MyProductController@storeImages')->name('images.store');
        Route::get('/options/images', 'MyProductController@getOptionsImages')->name('options.images');

        Route::post('/tags', 'MyProduct\TagController@create')->name('tag.create');
    });

    Route::post('/shopify/send', 'ShopifyMyProductController@sendMyProducts')->name('shopify.send');
});

// /api/products
Route::group(['namespace' => 'Api\Internal', 'prefix' => 'products', 'as' => 'products.'], function () {
    Route::get('/', 'ProductController@getProducts')->name('get');
    Route::get('/{product_id?}', 'ProductController@getProduct')->name('get');
    Route::get('/{product_id?}/images', 'ProductController@getProductImages')->name('images');
    Route::get('/{product_id?}/options/images', 'ProductController@getProductOptionsImages')->name('options.images');
    Route::post('/{product_id?}/my/add', 'MyProductController@addProduct')->name('add');
});

Route::group(['prefix' =>'/shopify/stockexchange', 'namespace' => 'Api\External\Shopify', 'as' => 'shopify_callback.', 'middleware' => ['ShopifyLog']], function () {
    Route::get('fetch_stock.json', 'StockUpdateController@index')->name('fetch_stock');
    Route::get('/', function () {
        abort(404);
    }); // нужно для правильной генерации урла route('shopify_callback.')
});
Route::get('/shopify{path}', function () {
    return ['status' => 200, 'data' => 'all ok'];
})->where('path', '.*')->middleware('ShopifyLog');
