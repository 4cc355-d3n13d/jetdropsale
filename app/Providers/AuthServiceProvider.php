<?php

namespace App\Providers;

use App\Models\User;
use App\Permissions\ModelPolicy;
use App\Permissions\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Model::class => ModelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        \Gate::before(function (User $user, $ability, $arguments) {
            return app(Permission::class)->check($user, $ability, $arguments);
        });
    }
}
