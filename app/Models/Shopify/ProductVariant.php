<?php

namespace App\Models\Shopify;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Models\Shopify\ProductVariant
 *
 * @property int $id
 * @property int $shopify_variant_id
 * @property int|null $product_variant_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class ProductVariant extends Model
{
    use InsertOnDuplicateKey;

    protected $table = 'shopify_product_variants';
    protected $guarded = [];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product\ProductVariant::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product\Product::class, 'product_id');
    }
}
