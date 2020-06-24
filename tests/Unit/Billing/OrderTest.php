<?php

namespace Tests\Unit\Billing;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Exceptions\OrderStatusException;
use App\Jobs\DeferredOrderPending;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Product\ProductVariant;
use App\Models\ShipGoods;
use App\Models\User;
use App\Models\Product\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class OrderTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testCreateHoldOrder()
    {
        Bus::fake();
        $order = create(Order::class);
        $this->assertEquals($order->status, OrderStatusType::HOLD);
        Bus::assertDispatched(DeferredOrderPending::class);
    }

    public function testCantChangeWrongStatus()
    {
        Bus::fake();
        $order = create(Order::class);
        $this->expectException(OrderStatusException::class);
        $order->changeStatus(OrderStatusType::REJECTED_INVOICE);
        $this->assertEquals($order->status, OrderStatusType::HOLD);
    }

    public function testCreateOrderWithNoCard()
    {
        $order = create(Order::class);
        $order->update([
            'auto_confirm_at' => now()->subMinute(1)
        ]);
        DeferredOrderPending::dispatch($order);
        $order->refresh();
        $this->assertEquals($order->status, OrderStatusType::NO_CARD);
    }

    public function testOrderStatusPause()
    {
        Bus::fake();
        $order = create(Order::class);
        $order->changeStatus(OrderStatusType::PAUSED, true);
        $this->assertEquals($order->status, OrderStatusType::PAUSED);
    }

    public function testOrderStatusCancel()
    {
        Bus::fake();
        $order = create(Order::class);
        $order->changeStatus(OrderStatusType::CANCELLED, true);
        $this->assertEquals($order->status, OrderStatusType::CANCELLED);
    }

    public function testOrderStatusCancelFromPause()
    {
        Bus::fake();
        $order = create(Order::class);
        $order->changeStatus(OrderStatusType::PAUSED, true);
        $order->changeStatus(OrderStatusType::CANCELLED, true);
        $this->assertEquals($order->status, OrderStatusType::CANCELLED);
    }

    public function testOrderConfirmedAndInvoiceOpen()
    {
        Bus::fake();
        /**
         * @var User $user
         */
        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        /**
         * @var Order $order
         */
        $order = create(Order::class, ['user_id'=>$user->id]);
        $this->assertEmpty($order->invoice);
        $order->changeStatus(OrderStatusType::PENDING, true);
        $this->assertEquals($order->status, OrderStatusType::CONFIRMED);
        $order->refresh(); // Attached invoice
        $this->assertNotEmpty($order->invoice);
        $this->assertEquals($order->invoice->status, InvoiceStatusType::OPEN);
        $this->assertEquals($order->price, $order->invoice->total_sum);
    }

    public function testOrderWithGoodsAndShipping()
    {
        /**
         * @var Order $order
         */
        $order = create(Order::class);
        $order->addToCart(create(Product::class));
        $this->assertCount(2, $order->cart);
        $ships = ShipGoods::first();
        $this->seeInDatabase('order_cart', [
                'goods_type' => ShipGoods::class,
                'amount' => $ships->getAmount(),
                'title' => $ships->getTitle(),
                'price' => $ships->getPrice(),
                'image' => $ships->getImage(),
        ]);
        $this->seeInDatabase('order_cart', ['goods_type' => Product::class]);
    }


    public function testWhenChangeCartThenChangeOrder()
    {
        $order = create(Order::class);
        $product = create(Product::class);
        $order->addToCart($product);
        $cart = OrderCart::where(['title' => $product->title, 'price' => $product->price, 'image' => $product->image ])->first();
        $this->assertNotEmpty($cart);
        $this->assertEquals($order->price, $product->price*$product->amount + Order::SHIPPING_PRICE);

        $product2 = create(Product::class);
        $variant = create(ProductVariant::class, ['product_id'=>$product2->id]);
        $cart->goods_type = get_class($variant);
        $cart->goods_id = $variant->id;
        $cart->save();

        $this->seeInDatabase('order_cart', ['title' => $product2->title, 'price' => $variant->price, 'image' => $variant->getImage() ]);
        $order->refresh();
        $this->assertEquals($order->price, $variant->price * $product->amount + Order::SHIPPING_PRICE);
    }

    public function testInvoiceOpenWith2Orders()
    {
        Bus::fake();
        /**
         * @var User $user
         */
        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        /**
         * @var Order $order
         */
        $order = create(Order::class, ['user_id'=>$user->id]);
        $order->addToCart(create(Product::class, ['price'=>1, 'amount'=>1]));
        $order->changeStatus(OrderStatusType::PENDING, true);

        $order2 = create(Order::class, ['user_id'=>$user->id]);
        $order2->addToCart(create(Product::class, ['price'=>2, 'amount' => 2]));
        $order2->changeStatus(OrderStatusType::PENDING, true);
        $this->assertEquals($order->status, OrderStatusType::CONFIRMED);
        $this->assertEquals($order2->status, OrderStatusType::CONFIRMED);
        $order->refresh();
        $order2->refresh();
        $this->assertNotEmpty($order->invoice);
        $this->assertNotEmpty($order2->invoice);

        $this->assertEquals($order->invoice->status, InvoiceStatusType::OPEN);
        $this->assertEquals($order->invoice->id, $order2->invoice->id);

        $this->assertEquals($order->price, OrderCart::where('order_id', $order->id)->get()->sum(function ($cart) {
            return $cart->price * $cart->amount;
        }));

        $total_sum = OrderCart::all()->sum(function ($cart) {
            return $cart->price * $cart->amount;
        });

        // 2 deliveries + 1 good for $1 + 2 goods for $2
        //$total_sum = 4.8 + 4.8 + 1 + 2*2;

        $this->assertEquals($total_sum, $order->invoice->fresh()->total_sum);
    }

    public function testInvoiceToPendingPay()
    {
        Bus::fake();
        /**
         * @var User $user
         */
        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        /**
         * @var Order $order
         */
        $order = create(Order::class, ['user_id'=>$user->id]);
        $order->addToCart(create(Product::class, ['price'=>1, 'amount'=>1]));
        $order->changeStatus(OrderStatusType::PENDING, true);
        $order2 = create(Order::class, ['user_id'=>$user->id]);
        $order2->addToCart(create(Product::class, ['price'=>2, 'amount' => 20]));

        $order2->changeStatus(OrderStatusType::PENDING, true);

        $this->assertCount(1, Invoice::all());
        $order->refresh();
        $this->assertEquals($order->invoice->status, InvoiceStatusType::AWAITING_PAYMENT);

        Bus::assertDispatched(ProcessInvoicePayment::class);
    }

    /**
     * при создании нового инвойса - рестартим все старые rejected инвойсы
     */
    public function testTryRestartRejectedInvoices()
    {
        Bus::fake();
        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        create(Invoice::class, ['status' => InvoiceStatusType::REJECTED, 'user_id'=>$user->id], 3);

        $this->assertCount(3, Invoice::where('status', InvoiceStatusType::REJECTED)->get());
        $order = create(Order::class, ['user_id'=>$user->id, 'status'=>OrderStatusType::PAUSED]);
        $order->changeStatus(OrderStatusType::PENDING, true, true);

        $this->assertCount(1, Invoice::all()); // 0 price invoices has been deleted - ok
        $this->assertCount(1, Invoice::where('status', InvoiceStatusType::OPEN)->get());
        //$this->assertCount(3, Invoice::where('status', InvoiceStatusType::AWAITING_PAYMENT)->get());
    }

    public function testIfInvoiceRejectedThenOrdersRejected()
    {
        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        create(Order::class, ['user_id'=>$user->id, 'status'=>OrderStatusType::PAUSED], 3)->each(function (Order $order) {
            $order->auto_confirm_at = now()->subMinute(1);
            $order->status = OrderStatusType::HOLD;
            $order->save();
            DeferredOrderPending::dispatch($order);
        });

        $this->assertCount(3, Order::where('status', OrderStatusType::CONFIRMED)->get());

        Order::first()->invoice->changeStatus(InvoiceStatusType::REJECTED, true);
        $this->assertCount(3, Order::where('status', OrderStatusType::REJECTED_INVOICE)->get());
    }

    /**
     * Если ордер перекинули в cancel то связанный инвойс надо пересчитать
     *
     */
    public function testOrderFromRejectedToCancel()
    {
        $ORDER_COUNT = 3;
        $PRODUCT_COUNT = 1;
        $PRODUCT_PRICE = 7;

        $user = create(User::class);
        create(Card::class, ['user_id'=>$user->id, 'primary'=>true]);
        create(Order::class, ['user_id'=>$user->id, 'status'=>OrderStatusType::PAUSED], $ORDER_COUNT)->each(function (Order $order) use ($PRODUCT_PRICE, $PRODUCT_COUNT) {
            $order->addToCart(create(Product::class, ['price'=>$PRODUCT_PRICE, 'amount'=>$PRODUCT_COUNT]));
            $order->changeStatus(OrderStatusType::PENDING, true, true);
        });

        $this->assertCount(3, Order::where('status', OrderStatusType::REJECTED_INVOICE)->get());

        $this->assertCount(1, Invoice::all());
        $this->assertEquals(Invoice::first()->status, InvoiceStatusType::REJECTED);

        $order = Order::first();
        $this->assertEquals(Invoice::first()->total_sum, $PRODUCT_PRICE*$PRODUCT_COUNT*$ORDER_COUNT+Order::SHIPPING_PRICE * $ORDER_COUNT);
        $order->changeStatus(OrderStatusType::CANCELLED, true, true);
        $ORDER_COUNT--;
        $this->assertEquals(Invoice::first()->total_sum, $PRODUCT_PRICE*$PRODUCT_COUNT*$ORDER_COUNT+Order::SHIPPING_PRICE * $ORDER_COUNT);
    }

    public function testIfInvoiceCanceledThenOrdersCanceled()
    {
        $invoice = create(Invoice::class, ['user_id'=>1, 'status'=>InvoiceStatusType::OPEN]);
        create(Order::class, ['user_id'=>1, 'status'=>OrderStatusType::CONFIRMED], 3)->each(function (Order $order) use ($invoice) {
            $invoice->addOrder($order);
        });

        $this->assertCount(3, Order::where('status', OrderStatusType::CONFIRMED)->get());
        $this->assertCount(1, Invoice::where('status', InvoiceStatusType::OPEN)->get());

        $invoice->status = InvoiceStatusType::CANCELED;
        $invoice->save();
        // Logic: Cancelling invoice - cancelling all orders (if they are not shipped already). Recount the invoice - if it`s empty it should be deleted
        $this->assertCount(3, Order::where('status', OrderStatusType::CANCELLED)->get());
        $this->assertCount(0, Invoice::where('status', InvoiceStatusType::CANCELED)->get());
        $this->seeInDatabase('invoices', ['id'=>1, 'status'=>InvoiceStatusType::CANCELED]);
    }

    public function testIfInvoiceCanceledThenShippedOrderNotCanceled()
    {
        Bus::fake();

        $invoice = create(Invoice::class, ['user_id'=>1, 'status'=>InvoiceStatusType::OPEN]);
        create(Order::class, ['user_id'=>1, 'status'=>OrderStatusType::CONFIRMED], 3)->each(function (Order $order) use ($invoice) {
            $invoice->addOrder($order);
        });
        create(Order::class, ['user_id'=>1, 'status'=>OrderStatusType::SHIPPED], 1)->each(function (Order $order) use ($invoice) {
            $invoice->addOrder($order);
        });
        create(Order::class, ['user_id'=>1, 'status'=>OrderStatusType::DELIVERED], 1)->each(function (Order $order) use ($invoice) {
            $invoice->addOrder($order);
        });

        $this->assertCount(3, Order::where('status', OrderStatusType::CONFIRMED)->get());
        $this->assertCount(1, Order::where('status', OrderStatusType::DELIVERED)->get());
        $this->assertCount(1, Order::where('status', OrderStatusType::SHIPPED)->get());
        $this->assertCount(1, Invoice::where('status', InvoiceStatusType::OPEN)->get());

        $invoice->update(['status' => InvoiceStatusType::CANCELED]);

        // Logic: Cancelling invoice - cancelling all orders (if they are not shipped already). Recount the invoice - if it`s empty it should be deleted
        $this->assertCount(3, Order::where('status', OrderStatusType::CANCELLED)->get());
        $this->assertCount(1, Invoice::where('status', InvoiceStatusType::CANCELED)->get());
        $this->assertCount(1, Order::where('status', OrderStatusType::DELIVERED)->get());
        $this->assertCount(1, Order::where('status', OrderStatusType::SHIPPED)->get());
    }
}
