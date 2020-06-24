<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class AuditOrder extends Resource
{
    public static $model = \OwenIt\Auditing\Models\Audit::class;
    public static $displayInNavigation = false;

    public function fields(Request $request)
    {
        return [
            DateTime::make('created_at')->sortable(),

            Text::make('event')->sortable(),

            Text::make('changes', function () {
                return view('admin.audits_order', ['audit' => $this])->render();
            })->asHtml(),
            ID::make('user', function () {
                return optional($this->user)->email;
            }),


          //  DateTime::make('updated_at')->sortable(),
        ];
    }
}
