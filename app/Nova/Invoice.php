<?php
namespace App\Nova;

use App\Enums\InvoiceStatusType;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Invoice extends Resource
{
    public static $model = \App\Models\Invoice::class;

    public static $search = ['id'];

    public static $group = 'Manage';

    public function fields(Request $request)
    {
        return [
            ID::make('id')->sortable(),
            ID::make('user_id')->sortable(),

            Currency::make('total_sum')->format(config('app.money.format'))->sortable(),

            Text::make('status', function () {
                /** @var \App\Models\Invoice $this */
                return InvoiceStatusType::getKey($this->status);
            })->sortable(),
            Text::make('payment_data', function () {
                /** @var \App\Models\Invoice $this */
                return view(
                    'admin.array',
                    [
                        'array' => $this->payment_data['error'] ?? $this->payment_data['message'] ?? ['id' => $this->payment_data['id']] ?? [],
                        'table' => false
                    ])->render();
            })->asHtml(),
            Code::make('content')->hideFromIndex(),
            DateTime::make('created_at')->sortable()->exceptOnForms(),
            DateTime::make('expire_at')->sortable()->hideFromIndex()->exceptOnForms(),
            DateTime::make('updated_at')->sortable()->hideFromIndex()->exceptOnForms(),
            DateTime::make('paid_at')->sortable()->exceptOnForms(),
            Select::make('status')->options(InvoiceStatusType::toSelectArray())->onlyOnForms(),

            HasMany::make('Orders'),
            HasOne::make('User'),
        ];
    }
}
