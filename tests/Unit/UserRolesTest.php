<?php

namespace Tests\Unit;

use App\Models\Product\MyProduct;
use App\Models\User\UserRole;
use App\Permissions\Roles;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class UserRolesTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;
    /**
     * @test
     */
    public function addRoles()
    {
        $this->signIn();
        auth()->user()->addRole(new Roles\OwnerRole());
        $this->dontSeeInDatabase('user_roles', ['user_id'=>auth()->id(), 'role_id'=>UserRole::getIdByTitle('Admin')]);
        $this->seeInDatabase('user_roles', ['user_id'=>auth()->id(), 'role_id'=>UserRole::getIdByTitle('Owner')]);
    }

    /**
     * @test
     */
    public function removeRole()
    {
        $this->signIn();
        auth()->user()->addRole(new Roles\AdminRole());
        $this->seeInDatabase('user_roles', ['user_id'=>auth()->id(), 'role_id'=>UserRole::getIdByTitle('Admin')]);

        auth()->user()->removeRole(new Roles\AdminRole());
        $this->dontSeeInDatabase('user_roles', ['user_id'=>auth()->id(), 'role_id'=>UserRole::getIdByTitle('Admin')]);
    }

    /**
     * @test
     */
    public function userAccess()
    {
        $notMyProduct = create(MyProduct::class);
        $this->assertFalse(\Gate::check("view", $notMyProduct));
        $this->assertFalse(\Gate::check("asd"));

        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);
        $notMyProduct = create(MyProduct::class);

        $this->assertTrue(auth()->user()->can("view", $myProduct));
        $this->assertFalse(auth()->user()->can("view", $notMyProduct));
        $this->assertFalse(auth()->user()->can("asd"));

        auth()->user()->addRole(new Roles\AdminRole());
        $this->assertTrue(auth()->user()->can("view", $myProduct));
        $this->assertTrue(auth()->user()->can("view", $notMyProduct));
        $this->assertTrue(auth()->user()->can("asd"));
    }
}
