<?php

namespace App\Services;

use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Enums\ShopifyStatusType;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Shopify;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ShopifyOrder
{
    public static function store(Shopify\Shop $shop, Collection $data, $force = false)
    {
        /** @var Order $userOrder */
        $userOrder = Order::firstOrNew([
            'origin' => OrderOriginType::SHOPIFY,
            'origin_id' => $data->get('id')
        ]);
        $financialStatus = mb_strtoupper($data->get('financial_status'));
        // If such order has already been and if it`s already in work - leave it as is
        if (in_array($userOrder->status, OrderStatusType::confirmedStatuses())) {
            if ($userOrder->status == OrderStatusType::CONFIRMED && collect($data->get('shipping_address'))->diffAssoc($userOrder->shipping_address)->isNotEmpty()) {
                $userOrder->shipping_address = $data->get('shipping_address');
                $userOrder->origin_status = $userOrder->origin_status =
                    collect(ShopifyStatusType::$shopifyStatuses)->get($financialStatus, ShopifyStatusType::UNKNOWN);
                $userOrder->save();
                return ['status' => Response::HTTP_OK, 'data' => 'Order confirmed, Shipping address updated'];
            }
            return ['status' => Response::HTTP_OK, 'data' => 'Order already confirmed ' . $userOrder->id];
        }

        // Check if we have the link
        $products = collect($data->get('line_items'));
        $productIds = $products->pluck('product_id')->unique();
        if (!$force && ! Shopify\ShopifyProduct::whereIn('shopify_id', $productIds)->get()->count()) {
            return ['status' => Response::HTTP_OK, 'data' => 'No products for order'];
        }

        // All right, save it
        try {
            $userOrder->shop_id = $shop->id;
            $userOrder->user_id = $shop->user_id;
            $userOrder->origin = OrderOriginType::SHOPIFY;
            $userOrder->origin_id = $data->get('id');
            $userOrder->origin_name = $data->get('order_number');
            $userOrder->origin_status =
                collect(ShopifyStatusType::$shopifyStatuses)->get($financialStatus, ShopifyStatusType::UNKNOWN);

            if (collect($data->get('billing_address'))->diffAssoc($userOrder->billing_address)->isNotEmpty()) {
                $userOrder->billing_address = $data->get('billing_address');
            }

            if (collect($data->get('shipping_address'))->diffAssoc($userOrder->shipping_address)->isNotEmpty()) {
                $userOrder->shipping_address = $data->get('shipping_address');
            }
            $needReview = false;
            $message = '';
            $productsInOrder = $products->map(
                function ($lineItem) use ($force, &$needReview, &$message) {
                    $lineItem = collect($lineItem);
                    $model = Shopify\ProductVariant::where('shopify_variant_id', $lineItem->get('variant_id', -1))->first();
                    if ($model) {
                        if (!$model->productVariant) {
                            $needReview = true;
                            $message = 'Нашли сам продукт, но не нашли комбинацию. Надо проверить';
                            $model = $model->product;
                        } else {
                            $model = $model->productVariant;
                        }
                    } else {
                        $model = Shopify\ShopifyProduct::where('shopify_id', $lineItem->get('product_id', -1))->first();
                        if ($model) {
                            $needReview = true;
                            $message = 'Нашли сам продукт, но не нашли комбинацию. Надо проверить';
                            $model = $model->myProduct->original;
                            if ($lineItem->has('variant_title')) {
                                $model->title .= ' (' . $lineItem->get('variant_title') . ')';
                            }
                        }
                    }
                    // @todo get rid of myproduct
                    if ($model) {
                        $model->amount = $lineItem->get('quantity');

                        return $model;
                    }

                    $needReview = true;
                    $message = 'В шопифай-ордере есть товар, который мы не распознали. Надо проврить';

                    if ($force) {
                        $model = new OrderCart([
                            'title' => $lineItem->get('title') . ' (' . $lineItem->get('variant_title') . ')',
                            'price' => $lineItem->get('price'),
                            'amount'=> $lineItem->get('quantity'),
                            'image' => "",
                        ]);
                        $message = 'Подгрузили товар из шопифай ордера без связи с нашим товаром. Надо найти наш товар и проверить цену';
                        return $model;
                    }

                    return null;
                }
            )->filter();

            // Set on pause first, fix the cart and go on
            if ($userOrder->wasRecentlyCreated) {
                $userOrder->status = OrderStatusType::PAUSED;
            }

            if ($productsInOrder->count()) {
                $userOrder->save();
            } else {
                throw new \RuntimeException('Won`t save the empty order =P');
            }

            $productsInOrder->each(function ($model, $key) use ($userOrder) {
                // If we have 2 similar products in order but we have no variants than we link the first product and leave the second as is
                $userOrder->addToCart($model, $key > 0 ? true : false);
            });
            if ($userOrder->wasRecentlyCreated) {
                if ($needReview) {
                    $userOrder->notes = $message;
                    $userOrder->changeStatus(OrderStatusType::CHECKING, true, true);
                } else {
                    $userOrder->changeStatus(OrderStatusType::HOLD, true, true);
                }
            }
        } catch (\Exception $e) {
            report($e);
            Log::channel('shopify')->error('Не удалось сохранить заказ', ['data' => $data]);
            Log::channel('shopify')->error((string) $e);
            return ['status' => Response::HTTP_NOT_IMPLEMENTED, 'data' => 'Cannot save the order'];
        }
        if ($userOrder->wasRecentlyCreated) {
            return ['status' => Response::HTTP_OK, 'data' => 'Order created ' . $userOrder->id];
        } elseif ($userOrder->wasChanged()) {
            return ['status' => Response::HTTP_OK, 'data' => 'Order updated ' . $userOrder->id];
        } else {
            return ['status' => Response::HTTP_OK, 'data' => 'Order not updated ' . $userOrder->id];
        }
    }
}
