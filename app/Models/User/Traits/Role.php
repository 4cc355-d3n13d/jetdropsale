<?php


namespace App\Models\User\Traits;

use App\Models\User\UserRole;
use App\Permissions\Roles\Role as PermissionRole;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Role
{
    /** @return PermissionRole[] */
    public function getRoles(): array
    {
        foreach ($this->roles as $userRole) {
            $userRole && $roles[] = new $userRole->role;
        }

        return $roles ?? [];
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function addRole(PermissionRole $role): void
    {
        UserRole::firstOrCreate(['user_id' => $this->id, 'role_id' => UserRole::getIdByTitle($role->getTitle())]);
        unset($this->roles);
    }

    public function removeRole(PermissionRole $role): void
    {
        optional(UserRole::find(['user_id' => $this->id, 'role_id' => UserRole::getIdByTitle($role->getTitle())])->first())->delete();
        unset($this->roles);
    }

    public function hasRole(PermissionRole $role): bool
    {
        return cache()->rememberForever(self::hasRoleCacheKey($this->id), function () use ($role) {
            return UserRole::where(['user_id' => $this->id, 'role_id' => UserRole::getIdByTitle($role->getTitle())])->exists();
        });
    }

    public static function hasRoleCacheKey($uid)
    {
        return 'user_has_roles_' . $uid;
    }
}
