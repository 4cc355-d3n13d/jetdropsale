<?php

namespace App\Models\User;

use App\Models\User;
use App\Permissions\Roles;
use App\SuperClass\Model;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\UserRole
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $role
 * @property-read mixed $title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class UserRole extends Model
{
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const ROLE_DEVELOPER = 3;
    const ROLE_MANAGER = 4;
    const ROLE_IMPERSONATE = 5;
    const ROLE_VIEW_FEATURES = 6;

    public static $mapRoles = [
        self::ROLE_ADMIN => [
            'title' => 'Admin',
            'description' => 'Project Admin',
            'class' => Roles\AdminRole::class
        ],
        self::ROLE_MANAGER => [
            'title' => 'Manager',
            'description' => 'Project Manager',
            'class' => Roles\ManagerRole::class
        ],
        self::ROLE_USER => [
            'title' =>  'Owner',
            'description' => 'Model owner',
            'class' => Roles\OwnerRole::class
            ],
        self::ROLE_DEVELOPER => [
            'title' =>  'Developer',
            'description' => 'Project developer',
            'class' => Roles\DeveloperRole::class
            ],

        self::ROLE_IMPERSONATE => [
            'title' =>  'Impersonate',
            'description' => 'Can user impersonate',
            'class' => Roles\ImpersonateRole::class
            ],
        self::ROLE_VIEW_FEATURES => [
            'title' =>  'ViewFeatures',
            'description' => 'View new features',
            'class' => Roles\ViewFeaturesRole::class
            ]
    ];

    protected $table = 'user_roles';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::saved(function (self $model) {
            cache()->forget(User::hasRoleCacheKey($model->user_id));
        });
    }

    public function getTitleAttribute()
    {
        return $this->role_id ? static::$mapRoles[$this->role_id]['title'] : '';
    }

    public function getRoleAttribute()
    {
        return static::$mapRoles[$this->role_id]['class'];
    }

    public static function getIdByTitle(string  $title)
    {
        foreach (static::$mapRoles as $id => $params) {
            if ($params['title'] == $title) {
                return $id;
            }
        }
        throw new NotFoundException('Role not found');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
