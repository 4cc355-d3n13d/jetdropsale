<?php

namespace App\Nova;

use App\Models\Product\Product;
use App\Models\Product\ProductVariant;
use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Cart extends Resource
{
    public static $model = \App\Models\OrderCart::class;
    public static $displayInNavigation = false;

    public function fields(Request $request)
    {
        return [
                ID::make('id')->sortable(),
                Image::make('image')->thumbnail(function () {
                    return $this->image;
                })->exceptOnForms(),

                Textarea::make('title')->alwaysShow(),
                Text::make('title', function () {
                    return str_limit($this->title, 35);
                })->onlyOnIndex(),

                BelongsTo::make('Order')->onlyOnDetail(),
                BelongsTo::make('User')->onlyOnDetail(),

                MorphTo::make('Goods')->types([
                    \App\Nova\Product::class,
                    \App\Nova\ProductVariant::class,
                    ShipGoods::class
                ])->searchable($this->goods_type != \App\Models\ShipGoods::class)->onlyOnForms()->nullable()
                    ->hideWhenCreating(), // Bad: https://github.com/laravel/nova-issues/issues/956


                Text::make('Product Id', function () {
                    return (($this->goods instanceof Product && $id = $this->goods->id) || ($this->goods instanceof ProductVariant && $id = $this->goods->product->id))
                            ? view('admin.product_links', ['id'=> $id])->render()
                            : ''
                        ;
                })->asHtml(),


                Url::make('Ali link', function () {
                    return  ($this->goods instanceof Product || $this->goods instanceof ProductVariant) ? $this->goods->getAliLink() : '';
                })->label(optional($this->goods)->ali_id)->clickable()->clickableOnIndex(),


                Text::make('combination', function () {
                    return $this->goods instanceof ProductVariant
                        ? view('admin.options', ['options' => $this->goods->options(), 'rem' => 2])->render()
                        : ''
                    ;
                })->asHtml()->onlyOnIndex(),

                Text::make('combination', function () {
                    return $this->goods instanceof ProductVariant
                        ? view('admin.options', ['options' => $this->goods->options(), 'rem' => 16])->render()
                        : ''
                    ;
                })->asHtml()->onlyOnDetail(),

                Currency::make('price')->format(config('app.money.format'))->sortable(),
                Number::make('amount'),
            ];
    }
}
