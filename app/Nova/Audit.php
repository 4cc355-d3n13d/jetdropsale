<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Audit extends Resource
{
    public static $model = \OwenIt\Auditing\Models\Audit::class;

    public function fields(Request $request)
    {
        return [
            ID::make('id')->sortable(),
            DateTime::make('created_at')->sortable(),
            Text::make('auditable_type'),
            Text::make('auditable_id'),
            Text::make('changes', function () {
                return view('admin.audits_order', ['audit' => $this])->render();
            })->asHtml(),
            Text::make('url'),
            Text::make('ip'),
            Text::make('event')->sortable(),

            ID::make('user', function () {
                return optional($this->user)->email;
            }),


          //  DateTime::make('updated_at')->sortable(),
        ];
    }
}
