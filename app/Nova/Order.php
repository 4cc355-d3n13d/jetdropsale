<?php

namespace App\Nova;

use App\Enums\CanadaStatesEnum;
use App\Enums\OrderStatusType;
use App\Enums\UsStatesEnum;
use App\Models\Order as OrderModel;
use App\Nova\Filters\OrderStatusFilter;
use Carlson\NovaLinkField\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inspheric\Fields\Indicator;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Order extends Resource
{
    public static $model = OrderModel::class;

    public static $group = 'Manage';

    public static $with = ['user', 'cart', 'invoice', 'shop'];

    public static $search = [
        'id', 'origin_id', 'user_email',
    ];

    public function fields(Request $request)
    {
        /** @var OrderModel $this */
        return [
            ID::make('id')->onlyOnDetail(),
            Link
                ::make('id')
                ->details([
                    'href' => '/nova/resources/orders/' . optional($this)->id,
                    'text' => optional($this)->id,
                    'newTab' => true
                ])->sortable()->onlyOnIndex()
            ,
            Url
                ::make('shopify_id', 'origin_id', function () {
                    /** @var OrderModel $this */
                    return route('data.shopify.orders', ['ids' => optional($this)->origin_id, 'shop' => optional($this->shop)->shop]);
                })
                ->label(optional($this)->origin_id .
                    (optional($this)->origin_name ? ' #' . optional($this)->origin_name : '')
                )
                ->clickableOnIndex()
                ->sortable()
                ->onlyOnIndex()
            ,

            Url::make('shopify_id', function () {
                /** @var OrderModel $this */
                return route('data.shopify.orders', ['ids' => optional($this)->origin_id, 'shop' => optional($this->shop)->shop]);
            })->label(optional($this)->origin_id)->clickable()->hideFromIndex(),

            Currency::make('Total', 'price')->format(config('app.money.format'))->sortable(),

            Indicator
                ::make('status', function () {
                    return OrderStatusType::getKey(optional($this)->status);
                })
                ->withoutLabels()
                ->colors([
                    OrderStatusType::getKey(OrderStatusType::CREATED) => 'beige',
                    OrderStatusType::getKey(OrderStatusType::HOLD) => 'grey',
                    OrderStatusType::getKey(OrderStatusType::PAUSED) => 'yellow',
                    OrderStatusType::getKey(OrderStatusType::PENDING) => 'indigo',
                    OrderStatusType::getKey(OrderStatusType::NO_CARD) => 'red',
                    OrderStatusType::getKey(OrderStatusType::CONFIRMED) => 'green',
                    OrderStatusType::getKey(OrderStatusType::SUSPENDED) => 'red',
                    OrderStatusType::getKey(OrderStatusType::SHIPPED) => 'teal',
                    OrderStatusType::getKey(OrderStatusType::DELIVERED) => 'blue',
                    OrderStatusType::getKey(OrderStatusType::REJECTED_INVOICE) => 'red',
                    OrderStatusType::getKey(OrderStatusType::CANCELLED) => 'black',
                ])
                ->sortable()
            ,
            Select
                ::make('status')->options(
                    collect(OrderStatusType::getNovaStatusList())
                    ->mapWithKeys(function ($key, $value) {
                        return [$key => $value];
                    })->toArray()
                )
                ->withMeta([
                    'extraAttributes' => [
                        'placeholder' => optional($this)->status
                    ]
                ])
                ->onlyOnForms()
            ,

            Text::make('vendor_order_id', function () {
                return view('admin.array_items', ['items' => explode(',', optional($this)->vendor_id)])->render();
            })->asHtml(),

            Textarea::make('vendor_order_id', 'vendor_id')->onlyOnForms(),

            Text::make('tracking', function () {
                return view('admin.array_items', ['items' => explode(',', optional($this)->tracking_number)])->render();
            })->asHtml(),

            Textarea::make('tracking', 'tracking_number')->onlyOnForms(),

            Text::make('shipping', function () {
                $shipping = $this->prepareShippingAddress(optional($this)->shipping_address);
                return view('admin.array', ['array' => $shipping, 'table' => true])->render();
            })->asHtml()->onlyOnDetail(),

            Text::make('user', function () {
                /** @var \App\Models\Order $this */
                return view('admin.user_info', ['id' => optional($this->user)->id, 'email' => optional($this->user)->email])->render();
            })->asHtml(),

            DateTime::make('created_at')->sortable(),
            DateTime::make('updated_at')->sortable()->hideFromIndex(),

            Textarea::make('notes')->alwaysShow(),

            HasMany::make('Cart'),
            HasOne::make('User'),
            HasOne::make('Shop'),
            HasOne::make('Invoice'),
            HasMany::make('Change log', 'audits', AuditOrder::class)
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new OrderStatusFilter(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    private function prepareShippingAddress($shipping): array
    {
        $shipping = Arr::wrap($shipping);
        foreach ($shipping as $key => &$value) {
            switch ($shipping['country']) {
                case 'US':
                    $value = ($key === 'state' && in_array($value, UsStatesEnum::getKeys())) ? UsStatesEnum::getValue($value): $value;
                    break;
                case 'CA':
                    $value = ($key === 'state' && in_array($value, CanadaStatesEnum::getKeys())) ? CanadaStatesEnum::getValue($value): $value;
                    break;
            }
            unset($value);
        }

        $ordered = [
            'name' => 'name',
            'firstname' => 'first_name',
            'first_name' => 'first_name',
            'lastname' => 'last_name',
            'last_name' => 'last_name',
            'phone' => 'phone',
            'fax' => 'phone',
            'email' => 'email',
            'company' => 'company',
            'company_name' => 'company',
            'addres' => 'address1',
            'address' => 'address1',
            'address1' => 'address1',
            'addres1' => 'address1',
            'addres2' => 'address2',
            'address2' => 'address2',
            'address3' => 'address3',
            'addrestype' => 'address_type',
            'addres_type' => 'address_type',
            'addresstype' => 'address_type',
            'address_type' => 'address_type',
            'city' => 'city',
            'province' => 'province',
            'province_code' => 'province_code',
            'county' => 'country',
            'zip' => 'zip',
            'postal_code' => 'zip',
            'zipcode' => 'zip',
            'zip_code' => 'zip',
            'state' => 'state',
            'country' => 'country',
            'country_code' => 'country_code',
            'latitude' => 'latitude',
            'longitude' => 'longitude'
        ];

        $orderedShipping = [];
        foreach ($ordered as $key => $value) {
            if (array_key_exists($key, $shipping) && !empty($shipping[$key])) {
                $orderedShipping[$ordered[$key]] = isset($orderedShipping[$ordered[$key]]) ?
                    trim($orderedShipping[$ordered[$key]] . ' '. $shipping[$key]) :
                    trim($shipping[$key]);
            }
        }
        return $orderedShipping;
    }
}
