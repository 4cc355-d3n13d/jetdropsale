<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\WebController;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

class CategoryController extends WebController
{
    use SEOTools;

    private const USA = 'United states';
    private const CHINA = 'China';

    /**
     * @param Category $category
     * @return Factory|View
     */
    public function products(Category $category)
    {
        $this->seo()
            ->setTitle($category->title . ' Dropshipping Supplier')
            ->setCanonical(route('category.products', $category))
            ->setDescription("Dropship top {$category->title} products from best dropshipping suppliers with Dropwow dropshipping app. Add high quality {$category->title} products to your Shopify store for best price. All the itrems meet Dropwow compliance requirements.  If you don't find the required product category, please let us know.")
            ->metatags()->setKeywords([
                "dropship {$category->title}",
                "find {$category->title} to sell",
                "wholesale {$category->title}",
                "top selling {$category->title} items"
            ]);

        $this->seo()->opengraph()
            ->setUrl(route('category.products', $category))
            ->addImage('http://dropwow.com/img/dropwow.png')
            ->setType('category');



        $perPage = (int) request('per_page', parent::DEFAULT_PER_PAGE);
        if (request()->has('query')) {
            $q = preg_replace('#[^a-zа-я0-9\s]#xui', '', request('query', '*'));
        } else {
            $q = '*';
        }
        $products = Product::search($q);

        $products->whereRegexp(
            'categoriesPath.keyword',
            sprintf('.*/%d\/.+|%d\/.+|.*/%d', $category->id, $category->id, $category->id)
        );

        switch (true) {
            case (request()->has('pmin') && request()->has('pmax')):
                $products
                    ->whereBetween('price', [
                        request('pmin'),
                        request('pmax'),
                    ]);
                break;
            case (request()->has('pmin')):
                $products
                    ->where('price', '>=', request('pmin'));
                break;
            case (request()->has('pmax')):
                $products
                    ->where('price', '<=', request('pmax'));
                break;
            default:
                break;
        }

        if (request()->has('ship_country')) {
            switch (request('ship_country')) {
                case 'usa':
                    $countryName = self::USA;
                    break;
                case 'china':
                    $countryName = self::CHINA;
                    break;
                default:
                    break;
            }
            /** @noinspection PhpUndefinedVariableInspection */
            if (isset($countryName)) {
                $products->whereMatch('ship_countries', $countryName);
            }
        };

        return view('product._product-category-list', [
            'result' => 'ok',
            'category' => $category,
            'categories' => $category->children,
            'products' => $products->paginate($perPage)
        ]);
    }

    public function search()
    {
        if (!request('query', false)) {
            return redirect('catalog');
        }

        $perPage = (int) request('per_page', 16);
        $q = preg_replace('#[^a-zа-я0-9\s]#xui', '', Input::get('query')) ?? '*';
        $products = Product::search($q);

        return view('product._product-search-results', [
            'result' => 'ok',
            'products' => $products->paginate($perPage)
        ]);
    }
}
