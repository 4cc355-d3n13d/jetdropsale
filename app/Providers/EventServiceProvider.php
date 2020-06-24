<?php

namespace App\Providers;

use App\Jobs\Shopify\InitShopifyHooks;
use App\Listeners\AuditedListener;
use App\Listeners\AuditingListener;
use App\Listeners\MyProductUpdatedListener;
use App\Listeners\MyProductVariantDeletingListener;
use App\Listeners\Order\AddedToInvoice;
use App\Listeners\Order\CartUpdateTitle;
use App\Listeners\Order\InvoiceAwaitingPayment;
use App\Listeners\Order\InvoiceCanceled;
use App\Listeners\Order\InvoiceChangeSum;
use App\Listeners\Order\InvoicePaid;
use App\Listeners\Order\InvoiceRejected;
use App\Listeners\Order\OrderCanceled;
use App\Listeners\Order\OrderHold;
use App\Listeners\Order\OrderPending;
use App\Listeners\Order\OrderRecalcSum;
use App\Listeners\Order\OrderRejected;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductVariant;
use App\Models\Shopify\Shop;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use OwenIt\Auditing\Events\Audited;
use OwenIt\Auditing\Events\Auditing;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Shopify\ShopifyExtendSocialite;

/**
 * Class EventServiceProvider
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        Auditing::class => [AuditingListener::class],
        Audited::class => [AuditedListener::class],
        SocialiteWasCalled::class => [ShopifyExtendSocialite::class],

        'eloquent.created: ' . Shop::class => [Shop::class . '@syncCsCart'],
        'eloquent.updated: ' . MyProduct::class => [MyProductUpdatedListener::class],
        // 'eloquent.updated: ' . MyProductVariant::class => [MyProductVariantUpdatedListener::class],
        'eloquent.deleting: ' . MyProductVariant::class => [MyProductVariantDeletingListener::class],

        'shopify.init-hooks' => [InitShopifyHooks::class],


        // Working with orders
        'order.cart.changing' => [CartUpdateTitle::class],
        'order.cart.changed' => [OrderRecalcSum::class],
        'order.status.hold' => [OrderHold::class],
        'order.status.paused' => [],
        'order.status.cancelled' => [OrderCanceled::class],
        'order.status.pending'  => [OrderPending::class],
        'order.status.no_card'  => [],
        'order.status.confirmed' => [],
        'order.status.rejected_invoice' => [],
        'order.status.refunded' => [OrderRejected::class],
        'order.status.failed' => [OrderRejected::class],

        'invoice.order_added'   => [AddedToInvoice::class],
        'invoice.change_sum'     => [InvoiceChangeSum::class],
        'invoice.status.awaiting_payment' => [InvoiceAwaitingPayment::class],
        'invoice.status.paid' => [InvoicePaid::class],
        'invoice.status.rejected' => [InvoiceRejected::class],
        'invoice.status.canceled' => [InvoiceCanceled::class],

        'user.balance.changed' => [],
    ];
}
