<?php

namespace App\Models\Product;

use App\Models\Shopify\Collection;
use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Product\MyProductCollection
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shopify\Collection $shopifyCollection
 * @property-read \App\Models\Product\MyProduct $products
 * @property-read \App\Models\User $user
 */
class MyProductCollection extends Model
{
    protected $table = 'my_collections';

    protected $guarded = [];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this
            ->belongsToMany(MyProduct::class, 'my_product_has_collections', 'my_collection_id', 'my_product_id')
            ->withTimestamps()
        ;
    }

    public function shopifyCollection(): HasOne
    {
        return $this->hasOne(Collection::class, 'my_collection_id', 'id');
    }
}
