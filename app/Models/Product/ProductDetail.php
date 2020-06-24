<?php

namespace App\Models\Product;

use App\SuperClass\Model;

/**
 * App\Models\Product\ProductDetail
 *
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model filter(\App\Filters\AbstractFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product\ProductDetail query()
 * @mixin \Eloquent
 */
class ProductDetail extends Model
{
    protected $table = 'product_details';
    protected $guarded = [];
}
