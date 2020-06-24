<?php

namespace App\SuperClass;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;

/**
 * Class AuthenticatableModel (User)
 *
 * @method bool|null forceDelete()
 * @method static bool|null restore()
 * @method static Builder|static onlyTrashed()
 * @method static Builder|static withTrashed()
 * @method static Builder|static withoutTrashed()
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 */
abstract class AuthenticatableModel extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    use SoftDeletes, Notifiable;
}
