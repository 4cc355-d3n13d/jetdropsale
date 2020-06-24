<?php

namespace App\Jobs\MarketSync;

use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Enums\ShopifyStatusType;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Product\Product;
use App\Models\Shopify\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \stdClass */
    private $response;

    /** @var Shop */
    private $shop;

    /** @var User */
    private $user;

    protected $importStatuses = [
        'P' => 'Processed',
        'C' => 'Completed',
        'O' => 'Open',
        'F' => 'Failed',
        'D' => 'Declined',
        'B' => 'Backordered',
        'I' => 'Canceled',
        'Y' => 'Awaiting call',
    ];

    protected $importVendorStatuses = [
        'P' => 'Processed',
        'C' => 'Created',
        'S' => 'Shipped',
        'D' => 'Delivered',
    ];


    public function __construct(Shop $shop, \stdClass $responseObject)
    {
        $this->shop = $shop;
        $this->user = $shop->user;
        $this->response = $responseObject;
    }

    public function handle(): void
    {
        if (empty($this->response->data->orders)) {
            return;
        }
        foreach ($this->response->data->orders as $importOrder) {
            /**
             * @var Order $userOrder
             */
            $userOrder = transform(Order::firstOrNew([
               // 'origin' => OrderOriginType::SHOPIFY,
                'origin_id' => empty($importOrder->shopify_order_id)? null : $importOrder->shopify_order_id,
                'shop_id' => $this->shop->id,
                'created_at' => Carbon::createFromTimestamp($importOrder->timestamp)
            ]), function (Order $order) use ($importOrder) {
                $order->shop_id = $this->shop->id;
                $order->user_id = $this->user->id;
                if ($importOrder->shopify_order_id) {
                    $order->origin = OrderOriginType::SHOPIFY;
                    $order->origin_id = $importOrder->shopify_order_id;
                } else {
                    $order->origin = 'Dropwow old';
                    $order->origin_id = null;
                }
                $order->origin_status = ShopifyStatusType::MIGRATED;
                $order->notes = "<a href='https://market.dropwow.com/c5643.php?dispatch=orders.details&order_id={$importOrder->order_id}' target='_blank'>cscart {$importOrder->order_id}</a>";
                $order->billing_address = $this->extractFieldsByPrefix($importOrder, 'b_');
                $order->shipping_address = $this->extractFieldsByPrefix($importOrder, 's_');
                $order->created_at = Carbon::createFromTimestamp($importOrder->timestamp);
                $order->vendor_id = $importOrder->vendor_order_id;
                switch ($importOrder->status) {
                    case 'Processed':
                        switch ($importOrder->vendor_status) {
                            case 'Processed':
                                $order->status = OrderStatusType::CONFIRMED;
                                break;
                            case 'Created':
                                $order->status = OrderStatusType::CREATED;
                                break;
                            case 'Shipped':
                                $order->status = OrderStatusType::SHIPPED;
                                break;
                            case 'Delivered':
                                $order->status = OrderStatusType::DELIVERED;
                                break;
                        }
                        break;
                    case 'Completed': // delivered
                        $order->status = OrderStatusType::CONFIRMED;
                        break;
                    case 'Open': // has rejected invoices
                        $order->status = OrderStatusType::PAUSED;
                        break;
                    case 'Failed': // failed (cannot process but can try again), remove from invoice
                    case 'Backordered': // Failed
                        $order->status = OrderStatusType::FAILED;
                        break;
                    case 'Canceled': // cancelled
                    case 'Declined': // canceled
                        $order->status = OrderStatusType::CANCELLED;
                        break;
                    case 'Awaiting call': // pending
                        $order->status = OrderStatusType::PENDING;
                        break;
                    default:
                        $order->status = OrderStatusType::PAUSED;
                        break;
                }

                if (!$importOrder->payment_id && !$order->status) {
                    $order->status = OrderStatusType::PAUSED;
                }

                return $order;
            });
            if (!$importOrder->company_id) {
                // Delete if was uploaded earlier
                $userOrder->forceDelete();
            } else {
                if (!$userOrder->id) {
                    // If order already was - we should not resave it again
                    $userOrder->save();
                }
            }


            collect($importOrder->ordered_products)->each(function ($productData, $i) use ($userOrder) {
                $product = Product::where(['ali_id' => $productData->ali_product_id])->first();
                $added = false;
                if ($product && $product->id) {
                    $product->price = $productData->price;
                    $product->amount = $productData->amount;
                    $added = $userOrder->addToCart($product);
                }
                if (!$added) {
                    tap(OrderCart::firstOrCreate([
                        'title' => $productData->title . ($i ? " ($i)" : ""),
                        'price' => $productData->price,
                        'amount'=> $productData->amount,
                        'user_id' => $userOrder->user_id,
                        'order_id' => $userOrder->id
                    ]), function (OrderCart $cart) use ($productData) {
                        if ($cart->wasRecentlyCreated) {
                            $cart->update(['image'=>$productData->image_path]);
                        }
                    });
                }
            });
        }
    }

    private function extractFieldsByPrefix(\stdClass $object, string $prefix = 'b_')
    {
        foreach ($object as $key => $value) {
            if (starts_with($key, $prefix)) {
                $result[str_replace($prefix, '', $key)] = $value;
            }
        }

        return $result ?? [];
    }
}
