<?php

namespace App\SuperClass;

use App\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Model
 * @see https://laravel.com/docs/5.7/eloquent
 *
 * General EloquentModel access methods:
 * @method static Builder|static get()
 * @method static Builder|static select(array|Collection $value)
 * @method static Builder|static create(array $value)
 * @method static Builder|static find($value = null)
 * @method static Builder|static findOrFail($value)
 * @method static Builder|static firstOr(array|Collection $value = null, $closure = null)
 * @method static Builder|static firstOrFail(array|Collection $value = null)
 * @method static Builder|static firstOrNew(array|Collection $value, array $value = [])
 * @method static Builder|static firstOrCreate(array|Collection $value, array $value = [])
 * @method static Builder|static updateOrCreate(array|Collection $value, array $value = [])
 *
 * @method static Builder|static where($value, $value = null, $value = null)
 * @method static Builder|static whereNull($value)
 * @method static Builder|static whereNotNull($value)
 * @method static Builder|static whereDate($value, $value)
 * @method static Builder|static whereMonth($value, $value)
 * @method static Builder|static whereDay($value, $value)
 * @method static Builder|static whereYear($value, $value)
 * @method static Builder|static whereTime($value, $value)
 * @method static Builder|static whereIn($value, array|Collection $value)
 * @method static Builder|static orWhere($value, array $value)
 * @method static Builder|static orWhereIn($value, array|Collection $value)
 * @method static Builder|static whereNotIn($value, array|Collection $value)
 * @method static Builder|static whereBetween($value, array $value)
 * @method static Builder|static whereNotBetween($value, array $value)
 * @method static Builder|static whereColumn($value, $value, $value = null)
 *
 * @method static Builder|static filter(AbstractFilter $filters) Apply filter
 * @method static Builder|static paginate($value = null)
 * @method static Builder|static inRandomOrder()
 * @method static Builder|static count()
 * @method static Builder|static oldest()
 * @method static Builder|static latest()
 * @method static Builder|static lastOrFail($column = 'created_at')
 *
 * More access methods (from ide_helper)...
 * @method static Builder|static newModelQuery()
 * @method static Builder|static newQuery()
 * @method static Builder|static query()
 *
 * Common Properties:
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * SoftDeletes:
 * @property Carbon|null $deleted_at
 * @method static bool|null restore() For soft-deletes only
 * @method static bool|null forceDelete() For soft-deletes only
 * @method static Builder|static onlyTrashed() For soft-deletes only
 * @method static Builder|static withTrashed() For soft-deletes only
 * @method static Builder|static withoutTrashed() For soft-deletes only
 *
 * EloquentModel trait:
 * @mixin EloquentModel
 */
abstract class Model extends EloquentModel
{
    public const TEXT_LENGTH = 64535; // 65535 length of mysql TEXT

    /**
     * Apply all relevant thread filters.
     */
    public function scopeFilter(Builder $query, AbstractFilter $filters): Builder
    {
        return $filters->apply($query);
    }

    /**
     * Scope a query to only include last entries.
     */
    public function scopeLast(Builder $query, string $column = 'created_at')
    {
        ! isset($this->$column) && $column = $this->getKeyName();

        return $query
            ->where($column, '!=', '')
            ->orderBy($column, 'desc')
            ->offset(0)->limit(1)->first()
        ;
    }

    /**
     * Scope a query to only include last entries.
     */
    public function scopeLastOrFail(Builder $query, string $column = 'created_at')
    {
        ! isset($this->$column) && $column = $this->getKeyName();

        return $query
            ->where($column, '!=', '')
            ->orderBy($column, 'desc')
            ->offset(0)->limit(1)->firstOrFail()
        ;
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.description')) {
            $data['old_values']['description'] = mb_substr($this->getOriginal('description'), 0, self::TEXT_LENGTH);
            $data['new_values']['description'] = mb_substr($this->getAttribute('description'), 0, self::TEXT_LENGTH);
        }

        return $data;
    }
}
