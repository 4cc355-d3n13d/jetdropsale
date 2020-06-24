<?php

namespace Tests\Feature\Billing;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Product\Product;
use App\Models\ShipGoods;
use App\Models\Shopify;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits;

class UserBalanceTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    /** @var PaymentService */
    protected $paymentService;


    public function setUp()
    {
        parent::setUp();

        $this->paymentService = app()->make(PaymentService::class);
    }

    public function testBalanceWithdraw()
    {
        $startBalance = 50;
        $deliveryPrice = (float) (new ShipGoods())->getPrice();

        $this->arrangeUserShop('test_shop.myshopify.com', false);
        $shop = Shopify\Shop::lastOrFail();
        $this->makeUserPayable($shop->user);
        $this->signIn($shop->user);

        DB::update('UPDATE users SET balance = ? WHERE id = ?;', [$startBalance, $shop->user->id]);
        $shop->user->refresh();

        Queue::fake();

        $orderPrice = 25;
        $orderPriceWithDelivery = $orderPrice + $deliveryPrice;

        $order = $this->arrangeEmptyOrder($shop);
        $order->addToCart(create(Product::class, ['price' => 25, 'amount' => 1]));
        $order->changeStatus(OrderStatusType::PENDING);
        $invoice = $order->refresh()->invoice;

        Queue::assertPushed(ProcessInvoicePayment::class, 1);
        $this->paymentService->payInvoice($invoice);

        $invoice->refresh();
        $shop->user->refresh();
        $startBalance -= $orderPriceWithDelivery;

        self::assertEquals(InvoiceStatusType::PAID, $invoice->status);
        self::assertEquals($orderPriceWithDelivery, $invoice->payment_structure['user_balance']);
        self::assertEquals($startBalance, $shop->user->balance);

        // Check that the invoice was paid, and the users balance is reduced
        // And the card was not touched...

        $orderPrice = 35;
        $orderPriceWithDelivery = $orderPrice + $deliveryPrice;

        $secondOrder = $this->arrangeEmptyOrder($shop);
        $secondOrder->addToCart(create(Product::class, ['price' => 35, 'amount' => 1]));
        $secondOrder->changeStatus(OrderStatusType::PENDING);
        $secondInvoice = $secondOrder->refresh()->invoice;

        Queue::assertPushed(ProcessInvoicePayment::class, 2);
        $this->paymentService->payInvoice($secondInvoice);

        $secondInvoice->refresh();
        $shop->user->refresh();

        // Check the invoice was paid, no money left on balance. Invoice was partially paid by card

        self::assertEquals(InvoiceStatusType::PAID, $secondInvoice->status);
        self::assertEquals($orderPriceWithDelivery, $secondInvoice->total_sum);
        self::assertEquals(15.8, $secondInvoice->payment_structure['card']);
        self::assertEquals(22.1, $secondInvoice->payment_structure['user_balance']);
        self::assertEquals(0, $shop->user->balance);
    }
}
