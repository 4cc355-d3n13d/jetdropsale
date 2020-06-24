<?php

namespace App\Http\Transformers;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use League\Fractal;

class MyProductTransformer extends Fractal\TransformerAbstract
{
    public function transform(MyProduct $myProduct)
    {
        return [
            'id' => (int) $myProduct->id,
            'title' => $myProduct->title,
            'status' => $myProduct->status,
            'is_shopify_send_pending' => $myProduct->status === MyProductStatusType::SHOPIFY_SEND_PENDING,
            'price' => $myProduct->price,
            'amount' => $myProduct->amount,
            'description' => $myProduct->description,
            'image' => $myProduct->image,
            'images' => $myProduct->images,
            'options' => $myProduct->options,
            'combinations' => $myProduct->combinations,
            'ali_id' => $myProduct->ali_id,
            'product_id' => $myProduct->product_id,
            'shopify_product_id' => $myProduct->shopify_product_id,
            'shopify_id' => $myProduct->shopifyProduct ? $myProduct->shopifyProduct->shopify_id : null,
            'product_categories_id' => $myProduct->product_categories_id,
            'user_id' => $myProduct->user_id,
            'created_at' => (string) $myProduct->created_at,
            'updated_at' => (string) $myProduct->updated_at,
            'sent_to_shopify_at' => (string) $myProduct->connected_at,
            'type' => (string) $myProduct->type,
            'vendor' => (string) $myProduct->vendor,
            'tags' => $myProduct->tags->pluck('title'),
            'collections' => $myProduct->collections->pluck('title', 'id'),
        ];
    }
}
