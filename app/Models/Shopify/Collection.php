<?php

namespace App\Models\Shopify;

use App\Models\Product\MyProductCollection;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Shopify\Collection
 *
 * @property int $id
 * @property int $my_collection_id
 * @property string $shopify_collection_id
 * @property string $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Shopify\Shop $shop
 * @property-read \App\Models\Product\MyProductCollection $myCollection
 * @mixin \Eloquent
 */
class Collection extends Model
{
    protected $table = 'shopify_collections';

    protected $guarded = [];


    public function myCollection(): BelongsTo
    {
        return $this->belongsTo(MyProductCollection::class, 'my_collection_id', 'id');
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }
}
