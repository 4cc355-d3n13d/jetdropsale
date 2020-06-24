<?php

namespace Tests\Feature\Billing;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Shopify;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits;

class PaymentFailureTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    private const SOURCE_REFERENCE = 'src_1D9EaTIJb8S3vLIy3zMX0I4y';
    private const CUSTOMER_REFERENCE = 'cus_DaPPcfkr32xm3T';


    public function testOrdersFailurePayment(): void
    {
        $this->arrangeUserShop('test_shop.myshopify.com', false);
        $shop = Shopify\Shop::lastOrFail();
        $this->arrangeOrderWithProducts($shop, 4);
        $this->arrangeOrderWithProducts($shop, 4);

        $this->signIn($shop->user);
        $this->putJson('/api/user/orders/1/status/PENDING');
        $order = Order::find(1);

        $this->assertEquals($order->status, OrderStatusType::NO_CARD);
        $this->assertCount(0, Invoice::all());

        // Payment of the first order should be retried
        $this->makeUserPayable($shop->user);
        $this->putJson('/api/user/orders/1/status/PENDING');

        $order->refresh();
        $this->assertEquals($order->status, OrderStatusType::CONFIRMED);
        $this->assertCount(1, Invoice::all());

        $this->seeInDatabase('invoices', [
            'id'      => 1,
            'user_id' => 1,
            'status'  => InvoiceStatusType::PAID,
        ]);

        $this->seeInDatabase('orders', [
            'id'      => 1,
            'user_id' => 1,
            'status'  => OrderStatusType::CONFIRMED,
        ]);

        $this->seeInDatabase('orders', [
            'id'      => 2,
            'user_id' => 1,
            'status'  => OrderStatusType::HOLD,
        ]);
    }
}
