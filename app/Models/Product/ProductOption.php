<?php

namespace App\Models\Product;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Product\ProductOption
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $value
 * @property string $image
 * @property int $ali_sku
 * @property int $ali_option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductOption withName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductOption withProduct($productId)
 * @mixin \Eloquent
 */
class ProductOption extends Model
{
    const SHIPPING_FROM_OPTION = 200007763;

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'image',
        'ali_sku',
        'ali_option_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param Builder $query
     * @param int $productId
     */
    public function scopeWithProduct($query, $productId): void
    {
        $query->whereHas('product', function ($q) use ($productId) {
            /** @var Builder $q */
            $q->where('product_id', $productId);
        });
    }

    /**
     * @param Builder $query
     * @param string $name
     */
    public function scopeWithName($query, $name): void
    {
        $query->where(['name' => $name]);
    }
}
