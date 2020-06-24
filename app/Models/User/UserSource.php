<?php

namespace App\Models\User;

use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\UserSources
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $path
 * @property string full_url
 * @property string $cookie_hash
 * @property string|null $http_referrer_domain
 * @property string|null $http_referrer_full
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_content
 * @property string|null $utm_term
 * @property string|null $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model filter(\App\Filters\AbstractFilter $filters)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\UserSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\UserSource newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User\UserSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\UserSource query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User\UserSource withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User\UserSource withoutTrashed()
 * @mixin \Eloquent
 */
class UserSource extends Model
{
    const DROPWOW_UUID_COOKIE = 'dropwow_uuid';

    protected $table = 'user_sources';

    protected $fillable = [
        'user_id',
        'path',
        'cookie_hash',
        'http_referrer_domain',
        'http_referrer_full',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'ip',
        'full_url'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
