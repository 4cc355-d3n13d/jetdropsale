<?php

namespace App\Models\Product;

use App\Jobs\HotCacheMenuCategories;
use App\SuperClass\Model;
use Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use ScoutElastic\Searchable;

/**
 * App\Models\Product\ProductCategory
 *
 * @property int $id
 * @property int $parent_id
 * @property int|null $ali_id
 * @property int|null $sort
 * @property string $title
 * @property string|null $ali_title
 * @property string|null $icon
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
class Category extends Model implements AuditableInterface
{
    use AuditableTrait;
    use Searchable;

    /** @var string $indexConfigurator */
    protected $indexConfigurator = ProductCategoryIndexConfigurator::class;

    protected $searchRules = [
        //
    ];

    protected $table = 'product_categories';


    protected $appends = ['path'];

    protected $mapping = [
        'properties' => [
            'text' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
        ]
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function () {
            cache()->forget(self::getMenuCacheKey());
            HotCacheMenuCategories::dispatch();
        });
        static::updated(function () {
            cache()->forget(self::getMenuCacheKey());
            HotCacheMenuCategories::dispatch();
        });
        static::deleted(function () {
            cache()->forget(self::getMenuCacheKey());
            HotCacheMenuCategories::dispatch();
        });
    }

    protected $guarded = [];

    public function getPathAttribute()
    {
        return route('category.products', ['category_id' => $this->id]);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }


    /**
     * @return HasMany|Product[]
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public static function getMenuCacheKey()
    {
        return 'main-categories';
    }

    /**
     * @return Category|Category[]|\Illuminate\Cache\CacheManager|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|mixed
     */
    public static function mainCategories()
    {
        $sqlOrderByRaw = 'sort IS NULL, sort ASC';
        $fields = ['id', 'parent_id', 'sort', 'icon', 'title', 'slug'];

        if (!($categories = cache(self::getMenuCacheKey()))) {
            $mainIds = [];
            $categories = Category::where(['parent_id'=>0])
                ->orderByRaw($sqlOrderByRaw)
                ->take(13)
                ->get($fields)
                ->map(function (Category $category) use ($sqlOrderByRaw, $fields, &$mainIds) {
                    $mainIds[] = $category->id;
                    $data = $category->toArray();
                    $data['children'] = Category::where(['parent_id'=>$category->id])
                        ->orderByRaw($sqlOrderByRaw)
                        ->take(6)
                        ->get($fields)
                        ->map(function (Category $category) use ($sqlOrderByRaw, $fields) {
                            $data = $category->toArray();
                            $data['children'] = Category::where(['parent_id'=>$category->id])
                                ->orderByRaw($sqlOrderByRaw)
                                ->take(5)
                                ->get($fields)->toArray();
                            return $data;
                        })->toArray();
                    return $data;
                })->toArray();

            // оставшиеся категории
            $allCategories = Category::where('parent_id', 0)->whereNotIn('id', $mainIds)
                ->orderByRaw($sqlOrderByRaw)
                ->get($fields)
                ->map(function (Category $category) use ($sqlOrderByRaw, $fields) {
                    $data = $category->toArray();
                    $data['children'] = Category::where(['parent_id'=>$category->id])
                        ->orderByRaw($sqlOrderByRaw)
                        ->take($category->id % 2  ? 1 : 2) // @костыль если всех по 2  - тогда в вслывашку не влезают. Некоторые по 1 некоторые по 2
                        ->get($fields)->toArray();
                    return $data;
                });


            array_push($categories, [
                "id" => 0,
                "parent_id" => 0,
                "sort" => 13,
                "icon" => 'fa-bars',
                "title" => "More categories",
                "slug" => "all-categories",
                "path" => "",
                "children" => $allCategories
            ]);
            cache()->forever(self::getMenuCacheKey(), $categories);
        }
        return $categories;
    }
}
