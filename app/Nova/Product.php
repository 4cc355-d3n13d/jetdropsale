<?php

namespace App\Nova;

use App\Enums\ProductStatusType;
use App\Models\Product\Product as ProductModel;
use App\Nova\Metrics\NewProducts;
use App\Nova\Metrics\ProductsPerDay;
use Carlson\NovaLinkField\Link;
use Dropwow\AddProduct\AddProductCard;
use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;

class Product extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = ProductModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'title';


    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        /** @var ProductModel $this */
        return [
            ID::make()->sortable(),
            Textarea::make('Title')->alwaysShow(),

            Avatar::make('Image')->thumbnail(function () {
                return $this->image;
            }),
            Select::make('Status')->options([ProductStatusType::AVAILABLE => 'Available', ProductStatusType::HIDDEN => 'Hidden'])->onlyOnForms(),

            Link::make('Title')
                ->details([
                    'href' => '/product/' . optional($this)->id,
                    'text' => str_limit($this->title, 30),
                    'newTab' => true
                ])->onlyOnIndex(),

            Text::make('Images', function () {
                /** @var ProductModel $this */
                if (json_decode($this->images)) {
                    /** @var ProductModel $this */
                    return view('admin.images', ['imgs' => json_decode($this->images)])->render();
                }
                return null;
            })->asHtml()->hideFromIndex(),

            Url::make('Ali link', function () {
                return 'https://alitems.com/g/1e8d1144949591a4efe116525dc3e8/?i=5&ulp=https://www.aliexpress.com/item//' . optional($this)->ali_id . '.html&aff_short_key=6iEY3FE';
            })->label(optional($this)->ali_id)->clickable()->clickableOnIndex(),

            Number::make('Amount'),
            Trix::make('Description')->hideFromIndex(),

            Currency::make('Price')->format(config('app.money.format'))->sortable(),

            DateTime::make('Created At')->sortable(),

            HasMany::make('Options', null, ProductOption::class),
            HasMany::make('Combinations', null, ProductVariant::class)
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(Request $request): array
    {
        return [
            new NewProducts,
            new ProductsPerDay,
            new AddProductCard,
        ];
    }
}
