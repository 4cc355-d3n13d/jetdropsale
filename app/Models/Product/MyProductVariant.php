<?php

namespace App\Models\Product;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Models\Product\MyProductVariant
 *
 * @property int $id
 * @property int|null $my_product_id
 * @property int|null $product_variant_id
 * @property string $sku
 * @property int $amount
 * @property float $price
 * @property mixed $combination
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \App\Models\Product\ProductVariant $original
 * @property-read \App\Models\Product\MyProduct|null $myProduct
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class MyProductVariant extends Model implements AuditableInterface
{
    use AuditableTrait;
    use InsertOnDuplicateKey;

    protected $fillable = [
        'my_product_id',
        'product_variant_id',
        'sku',
        'amount',
        'price',
        'combination',
    ];

    public function myProduct(): BelongsTo
    {
        return $this->belongsTo(MyProduct::class);
    }

    public function original(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getOriginalPriceAttribute(): float
    {
        return $this->original->price;
    }
}
