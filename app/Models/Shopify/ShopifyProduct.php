<?php

namespace App\Models\Shopify;

use App\Models\Product\MyProduct;
use App\Models\Product\Product;
use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Shopify\ShopifyProducts
 *
 * @property int $id
 * @property int $status
 * @property int|null $user_id
 * @property int|null $shopify_id
 * @property int|null $product_id
 * @property int|null $combination_id
 * @property int|null $my_product_id
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Product\Product|null $product
 * @property-read \App\Models\Product\MyProduct|null $myProduct
 * @mixin \Eloquent
 */
class ShopifyProduct extends Model
{
    protected $table = 'shopify_products';

    protected $fillable = [
        'shopify_id',
        'product_id',
        'my_product_id',
        'combination_id',
        'user_id',
        'status',
    ];

    protected $dates = ['updated_at', 'created_at'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function myProduct(): BelongsTo
    {
        return $this->belongsTo(MyProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
