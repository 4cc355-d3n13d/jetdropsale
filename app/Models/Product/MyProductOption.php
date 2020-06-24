<?php

namespace App\Models\Product;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\Product\MyProductOption
 *
 * @property int $id
 * @property int|null $my_product_id
 * @property string $name
 * @property string $value
 * @property string $image
 * @property int $ali_sku
 * @property int $ali_option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \App\Models\Product\MyProduct|null $myProduct
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\MyProductOption withName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\MyProductOption withMyProduct($myProductId)
 * @mixin \Eloquent
 */
class MyProductOption extends Model implements AuditableInterface
{
    use AuditableTrait;

    protected $fillable = [
        'my_product_id',
        'name',
        'value',
        'image',
        'ali_sku',
        'ali_option_id',
    ];

    public function myProduct(): BelongsTo
    {
        return $this->belongsTo(MyProduct::class);
    }

    /**
    * @param Builder $query
    * @param int $myProductId
    */
    public function scopeWithMyProduct($query, $myProductId): void
    {
        $query->whereHas('myProduct', function ($q) use ($myProductId) {
            /** @var Builder $q */
            $q->where('my_product_id', $myProductId);
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
