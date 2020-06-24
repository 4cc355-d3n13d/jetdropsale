<?php

namespace App\Nova;

use App\Models\User\UserRole;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Role extends Resource
{
    public static $model = \App\Models\User\UserRole::class;
    public static $displayInNavigation = false;

    public function fields(Request $request)
    {
        return [
            Text::make('Role', function () {
                return view('admin.role', ['title'=>$this->title])->render();
            })->exceptOnForms()->asHtml(),

            Select::make('Role', 'role_id')->options(
                 collect(UserRole::$mapRoles)->mapWithKeys(
                    function ($value, $key) {
                        return [$key => $value['title']];
                    }
                )->toArray()
            )->onlyOnForms()->canSeeWhen('UpdateUserRoles'),

            BelongsTo::make('User')->exceptOnForms(),
        ];
    }
}
