<?php

namespace Tests\Feature\Billing;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Enums\ShopifyStatusType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Shopify;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits;

class InvoicePaymentTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    private const SOURCE_REFERENCE = 'src_1D9EaTIJb8S3vLIy3zMX0I4y';
    private const CUSTOMER_REFERENCE = 'cus_DaPPcfkr32xm3T';


    public function testInvoiceAutoPayment(): void
    {
        $this->arrangeUserShop();
        $this->arrangeShopGoods();
        $shop = Shopify\Shop::lastOrFail();
        $user = $shop->user;
        self::assertEquals(20, $user->credit_limit);

        /** @var Invoice $invoice */
        $invoice = create(Invoice::class, ['user_id' => $user->id, 'total_sum' => 19, 'status' => InvoiceStatusType::OPEN]);
        create(Order::class, [
            'shop_id'          => $shop->id,
            'user_id'          => $user->id,
            'origin'           => OrderOriginType::SHOPIFY,
            'origin_id'        => mt_rand(111, 222),
            'origin_status'    => ShopifyStatusType::PARTIALLY_PAID,
            'invoice_id'       => $invoice->id,
        ]);

        /** @var Order $order */
        $order = create(Order::class, [
            'shop_id'          => $shop->id,
            'user_id'          => $user->id,
            'origin'           => OrderOriginType::SHOPIFY,
            'origin_id'        => 1,
            'origin_status'    => ShopifyStatusType::PARTIALLY_PAID,
        ]);
        $order->addToCart(create(Product::class, ['price'=>40, 'amount'=>1]));

        $order->changeStatus(OrderStatusType::PENDING);

        self::assertCount(1, Invoice::all());
        $invoice->refresh();

        self::assertEquals(InvoiceStatusType::PAID, $invoice->status);

        /** @var Order $order */
        $order = create(Order::class, [
            'shop_id'          => $shop->id,
            'user_id'          => $user->id,
            'origin'           => OrderOriginType::SHOPIFY,
            'origin_id'        => 2,
            'origin_status'    => ShopifyStatusType::PARTIALLY_PAID,
        ]);
        $order->addToCart(create(Product::class, ['price'=>10, 'amount'=>1]));
        $order->changeStatus(OrderStatusType::PENDING);
        /** @var Invoice $newInvoice */
        $newInvoice = Invoice::where(['status' => InvoiceStatusType::OPEN, 'user_id' => $user->id])->lastOrFail();

        // Manually pay the invoice...
        self::assertTrue(app()->make(PaymentService::class)->payInvoice($newInvoice));
        self::assertSame($newInvoice->expire_at->format('Y-m-t'), now()->format('Y-m-t'));
        self::assertEquals(InvoiceStatusType::PAID, $newInvoice->status);
        self::assertEquals(20, $user->credit_limit);
    }

    public function testInvoicePaymentSchedule(): void
    {
        $this->arrangeUserShop();
        $shop = Shopify\Shop::lastOrFail();

        /** @var Invoice $invoice */
        $invoice = create(Invoice::class, ['user_id' => $shop->user->id, 'total_sum' => 19, 'status' => InvoiceStatusType::OPEN]);
        self::assertSame(InvoiceStatusType::OPEN, $invoice->status);

        // Make it expire
        $invoice->update(['expire_at' => (now())->sub(new \DateInterval('P1M1D'))]);

        // Pay all expired invoices via command
        $this->artisan('billing:schedule');
        $invoice->refresh();

        self::assertEquals(InvoiceStatusType::PAID, $invoice->status);
        self::assertEquals(20, $invoice->user->credit_limit);
    }
}
