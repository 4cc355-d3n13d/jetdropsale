<?php

namespace App\Models\Product;

use App\Models\Shopify\ShopifyProduct;
use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\Product\MyProduct
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property array|mixed $images
 * @property int $amount
 * @property int $status
 * @property float $price
 * @property int|null $ali_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $shopify_product_id
 * @property int|null $product_categories_id
 * @property string|null $type
 * @property string|null $vendor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Carbon\Carbon $connected_at
 * @property-read \App\Models\User|null $user Really nullable?
 * @property-read \App\Models\Product\Product $original
 * @property-read \App\Models\Shopify\ShopifyProduct $shopifyProduct
 * @property-read \App\Models\Product\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductVariant[] $combinations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductOption[] $options
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\ProductDetail[] $details
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductCollection[] $collections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductTag[] $tags
 */
class MyProduct extends Model implements AuditableInterface
{
    use AuditableTrait;
    use ShopifyProductTrait;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'status',
        'price',
        'amount',
        'description',
        'ali_id',
        'image',
        'images',
        'user_id',
        'product_id',
        'shopify_product_id',
        'type',
        'vendor',
    ];

    protected $dates = ['deleted_at', 'updated_at', 'created_at', 'connected_at'];

    // protected $casts = [
    //     'images' => 'array',
    // ];

    public static function boot()
    {
        parent::boot();

        parent::created(function () {
            cache()->forget('non-connected-count.' . auth()->id());
            cache()->forget('connected-count.' . auth()->id());
        });

        parent::updated(function (self $model) {
            if ($model->isDirty('status')) {
                cache()->forget('non-connected-count.' . auth()->id());
                cache()->forget('connected-count.' . auth()->id());
            }
        });

        parent::deleted(function () {
            cache()->forget('non-connected-count.' . auth()->id());
            cache()->forget('connected-count.' . auth()->id());
        });
    }

    /**
     * @return HasMany|MyProductOption[]
     */
    public function options()
    {
        return $this->hasMany(MyProductOption::class);
    }

    /**
     * @return HasMany|MyProductVariant[]
     */
    public function combinations()
    {
        return $this->hasMany(MyProductVariant::class);
    }

    public function getVariant(int $id): MyProductVariant
    {
        return $this->combinations()->where('id', $id)->first();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function path(string $action = ''): string
    {
        return "/api/my-products/{$this->id}/{$action}";
    }

    public function shopifyProduct(): HasOne
    {
        return $this->hasOne(ShopifyProduct::class)->latest();
    }

    public function original(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProductDetail::class, 'product_id', 'product_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(MyProductTag::class);
    }

    public function collections(): BelongsToMany
    {
        return $this
            ->belongsToMany(MyProductCollection::class, 'my_product_has_collections', 'my_product_id', 'my_collection_id')
            ->withTimestamps()
        ;
    }

    public function transformAudit(array $data): array
    {
        return parent::transformAudit($data);
    }
}
