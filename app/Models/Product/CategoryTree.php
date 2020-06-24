<?php

namespace App\Models\Product;

/**
 * App\Models\Product\ProductCategoryTree
 *
 * @property int $id
 * @property int $parent_id
 * @property int|null $ali_id
 * @property string $title
 * @property string|null $ali_title
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \App\Models\Product\Category $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class CategoryTree extends Category
{
    protected $table = 'product_categories';
    protected $guarded = [];
}
