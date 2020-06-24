<?php

namespace Tests\Feature\Billing;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Enums\ShopifyStatusType;
use App\Exceptions\OrderStatusException;
use App\Jobs\DeferredOrderPending;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Shopify\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Tests\Traits;

class OrderLifecycleTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    // use Traits\UsesSqlite;

    private $orderNumber = 3146; // any

    protected function arrangeOrder(Shop $shop): Order
    {
        return create(Order::class, [
            'shop_id'          => $shop->id,
            'user_id'          => $shop->user_id,
            'origin'           => OrderOriginType::SHOPIFY,
            'origin_id'        => $this->orderNumber,
            'origin_name'      => $this->orderNumber,
            'origin_status'    => ShopifyStatusType::PARTIALLY_PAID,
            'auto_confirm_at'  => now()->addMinutes(2)
        ]);
    }

    public function testNaturalShopifyOrderWithSyncQueueWorkflow()
    {
        Bus::fake();
        $this->arrangeUserShop();
        $this->arrangeShopGoods();

        $shop = tap(Shop::lastOrFail(), function (Shop $shop) {
            $this->arrangeOrder($shop);
            $this->assertCount(1, Order::all());
            $this->assertCount(1, OrderCart::all());
            $this->sendShopifyOrder($shop->shop)->seeJson(['status' => 200, 'data' => 'Order created ' . Order::lastOrFail()->id]);
        });

        // At the end we need to make sure that products linked to the order has appeared
        $this->assertCount(2, Order::all());
        $this->assertCount(3, OrderCart::all()); // 2 deliveries; 1 product
        $this->seeInDatabase('orders', ['id' => 1, 'user_id' => 1, 'origin_id' => $this->orderNumber, 'origin_name' => $this->orderNumber, 'origin_status' => ShopifyStatusType::PARTIALLY_PAID]);
        $this->seeInDatabase('orders', ['id' => 2, 'user_id' => 1, 'origin_id' => 580244734022, 'origin_name' => 1118, 'origin_status' => ShopifyStatusType::PAID]);

        // Send once again - any new orders should appear
        $this->sendShopifyOrder($shop->shop);
        $this->assertCount(2, Order::all());
        $this->assertCount(0, Invoice::all());
        $this->assertCount(3, OrderCart::all());
    }

    public function testSyntheticOrderWithFakeQueueWorkflow()
    {
        Bus::fake();

        $this->arrangeUserShop();
        $this->arrangeShopGoods();
        $order = $this->arrangeOrder(Shop::lastOrFail());

        Bus::assertDispatched(DeferredOrderPending::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        }); // Assert the job was dispatched...
        Bus::assertNotDispatched(ProcessInvoicePayment::class); // Assert a job was not dispatched...

        self::assertSame($order->status, OrderStatusType::HOLD);
        $order->refresh();
        self::assertTrue(now()->addMinutes($order->user->setting('order_hold_time'))->diffInMinutes(now()->addMinutes($order->user->setting('order_hold_time'))) <= 10);
        self::assertNull($order->invoice);
    }

    public function testSyntheticOrderErrorWorkflow()
    {
        Bus::fake();
        $this->arrangeUserShop();
        $this->arrangeShopGoods();
        $order = $this->arrangeOrder(Shop::lastOrFail());
        self::assertSame(OrderStatusType::HOLD, $order->status);

        $this->expectException(OrderStatusException::class);
        $this->expectExceptionMessage('Wrong status transition requested');
        $order->changeStatus(OrderStatusType::DELIVERED);
    }

    public function testInvoiceManualSpawn()
    {
        Bus::fake();
        $this->arrangeUserShop()->arrangeShopGoods();
        $order = $this->arrangeOrder(Shop::lastOrFail());

        $this->assertCount(0, Invoice::all());
        $order->changeStatus(OrderStatusType::PENDING);
        $this->seeInDatabase('invoices', [
            'id' => 1, 'user_id' => 1, 'status' => InvoiceStatusType::OPEN
        ]);
    }

    public function testInvoiceAutoSpawn()
    {
        $this->arrangeUserShop()->arrangeShopGoods();
        $order = $this->arrangeOrder(Shop::lastOrFail());

        $this->assertCount(0, Invoice::all());

        $this->assertEquals($order->status, OrderStatusType::HOLD);
        $order->update(['auto_confirm_at' => now()->subMinute()]);
        DeferredOrderPending::dispatch($order);
        $order->refresh();
        $this->assertEquals($order->status, OrderStatusType::CONFIRMED);

        $invoice = $order->invoice;

        $this->assertEquals(1, $invoice->id);
        $this->assertEquals(1, $invoice->user_id);
        $this->assertEquals(InvoiceStatusType::OPEN, $invoice->status);
        $this->assertEquals(Order::SHIPPING_PRICE, $invoice->total_sum);
    }
}
