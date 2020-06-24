<?php

use \App\Http\Middleware\ShopifyAuth;
use \Illuminate\Routing\Router;

/**
 * Dropwow External REST API Routes
 */

/** @var Router $router */
$router->group([
    'name' => 'ali.',
    'namespace' => 'Api\External',
    'prefix' => 'ali',
    'as' => 'aliexpress.'
], function (Router $router) {
    // сохраняем продукты от бота (пока только али)
    $router->post('save', 'AliexpressController@save')->name('save');
    $router->get('list', 'AliexpressController@list')->name('list');
    $router->post('delete', 'AliexpressController@delete')->name('delete');
    ;
});


$router->group([
    'name' => 'shopify.',
    'namespace' => 'Api\External\Shopify',
    'middleware' => [ShopifyAuth::class, 'ShopifyLog'],
    'prefix' => 'shopify'
], function (Router $router) {

    //TODO remove old routes
    $router->any('order', 'OrderController@create');
    $router->any('delete', 'ProductsController@delete');

    $router->group(['name' => 'app.', 'prefix' => 'app'], function (Router $router) {
        $router->any('/uninstalled', 'StubController@index');
    });
    $router->group(['name' => 'carts.', 'prefix' => 'carts'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'checkouts.', 'prefix' => 'checkouts'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'collections.', 'prefix' => 'collections'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'collection_listings.', 'prefix' => 'collection_listings'], function (Router $router) {
        $router->any('/add', 'StubController@index');
        $router->any('/remove', 'StubController@index');
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'customers.', 'prefix' => 'customers'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/disable', 'StubController@index');
        $router->any('/enable', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'customer_groups.', 'prefix' => 'customer_groups'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'draft_orders.', 'prefix' => 'draft_orders'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'fulfillments.', 'prefix' => 'fulfillments'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'fulfillment_events.', 'prefix' => 'fulfillment_events'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'inventory_items.', 'prefix' => 'inventory_items'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'inventory_levels.', 'prefix' => 'inventory_levels'], function (Router $router) {
        $router->any('/connect', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/disconnect', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'locations.', 'prefix' => 'locations'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'orders.', 'prefix' => 'orders'], function (Router $router) {
        $router->any('/cancelled', 'StubController@index');
        $router->any('/create', 'OrderController@create');
        $router->any('/fulfilled', 'StubController@index');
        $router->any('/paid', 'StubController@index');
        $router->any('/partially_fulfilled', 'StubController@index');
        $router->any('/updated', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
    $router->group(['name' => 'order_transactions.', 'prefix' => 'order_transactions'], function (Router $router) {
        $router->any('/create', 'StubController@index');
    });
    $router->group(['name' => 'products.', 'prefix' => 'products'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'ProductsController@delete');
    });
    $router->group(['name' => 'product_listings.', 'prefix' => 'product_listings'], function (Router $router) {
        $router->any('/add', 'StubController@index');
        $router->any('/remove', 'StubController@index');
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'refunds.', 'prefix' => 'refunds'], function (Router $router) {
        $router->any('/create', 'StubController@index');
    });
    $router->group(['name' => 'shop.', 'prefix' => 'shop'], function (Router $router) {
        $router->any('/update', 'StubController@index');
    });
    $router->group(['name' => 'themes.', 'prefix' => 'themes'], function (Router $router) {
        $router->any('/create', 'StubController@index');
        $router->any('/publish', 'StubController@index');
        $router->any('/update', 'StubController@index');
        $router->any('/delete', 'StubController@index');
    });
});


$router->any('/shopify', function () {
    if (request()->has('hmac')) {
        return redirect(env('APP_URL') . '/login/shopify/?' . request()->getQueryString());
    }
    return redirect(env('APP_URL'));
})->middleware(['ShopifyLog']);

$router->get('/category/{category}', 'Product\CategoryController@products')->name('category.products');
