<?php

namespace App\Models;

use App\SuperClass\Model;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string|null $namespace
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model filter(\App\Filters\AbstractFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting query()
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $table = "settings";
    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();

        self::deleted(function () {
            cache()->forget(self::getCacheKey());
        });

        self::saved(function () {
            cache()->forget(self::getCacheKey());
        });
    }

    public static function all($columns = ['*'])
    {
        if (!($settings = cache(self::getCacheKey()))) {
            $settings = parent::all($columns);
            cache()->forever(self::getCacheKey(), $settings);
        }
        return $settings;
    }

    public static function getCacheKey()
    {
        return "all-setting-cache-key";
    }
}
