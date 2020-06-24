<?php

namespace App\Nova;

use App\Http\Controllers\Api\Internal\User\UserController;
use App\Models\User as UserModel;
use App\Permissions\Roles\Role;
use Carlson\NovaLinkField\Link;
use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = UserModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    public static $with = ['roles'];

    public static $group = 'Manage';

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6'),

            Text::make('Role', function () {
                /** @var \App\Models\User $this */
                return collect($this->getRoles())->map(function (Role $role) {
                    return view('admin.role', ['title' => $role->getTitle()])->render();
                })->implode(' ');
            })->asHtml()->canSeeWhen('viewUserRole'),

            Textarea::make('notes')->alwaysShow(),
            Textarea::make('notes')->onlyOnIndex(),

            DateTime::make('Created At')->sortable(),


            Url::make('Log as', function () {
                return route('impersonate.start', [$this]);
            })
                ->label('Login')
                ->canSeeWhen('canImpersonate')
                ->clickable()
                ->clickableOnIndex(),

            HasMany::make('Roles'),
            HasMany::make('Orders'),
            HasMany::make('Invoices'),
            HasMany::make('Shops')
        ];
    }

    /**
     * Get the cards available for the request.
     * @param Request $request
     * @return array
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(Request $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(Request $request): array
    {
        return [];
    }
}
