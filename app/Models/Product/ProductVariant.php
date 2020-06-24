<?php

namespace App\Models\Product;

use App\Models\GoodsContract;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Yadakhov\InsertOnDuplicateKey;

/**
 * App\Models\Product\ProductVariant
 *
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property int $amount
 * @property float $price
 * @property mixed $combination
 * @property string $title
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductVariant withProduct($productId)
 * @mixin \Eloquent
 */
class ProductVariant extends Model implements GoodsContract
{
    use InsertOnDuplicateKey;

    protected $fillable = [
        'product_id',
        'sku',
        'amount',
        'price',
        'combination',
    ];

    protected $appends = ['title', 'image'];

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

    public function getTitle()
    {
        return $this->title;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function options()
    {
        $self = $this;
        return cache()->remember('product.' . $this->id . '.' . crc32($this->combination), 60*24, function () use ($self) {
            $combination = collect(json_decode($this->combination));

            $query = ProductOption::where(['product_id'=>$this->product_id])->where(
                function (Builder $query) use ($combination) {
                    $combination->each(function ($ali_sku, $ali_option_id) use ($query) {
                        $query->orWhere(function (Builder $query) use ($ali_sku, $ali_option_id) {
                            $query->where(['ali_sku' => $ali_sku, 'ali_option_id' => $ali_option_id]);
                        });
                    });
                }
            );
            return $query->get()->toArray();
        });
    }

    public function getTitleAttribute()
    {
        return $this->product->title;
    }


    public function getImageAttribute()
    {
        return collect($this->options())->first(function ($option) {
            return isset($option['image']) && $option['image'];
        })['image'] ?? $this->product->image;
    }

    public function getAliLink()
    {
        return $this->product->getAliLink();
    }

    public function getAliIdAttribute()
    {
        return $this->product->ali_id;
    }
}
