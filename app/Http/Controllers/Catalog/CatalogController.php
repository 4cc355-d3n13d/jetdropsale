<?php

namespace App\Http\Controllers\Catalog;

use App\Models\Product\Product;
use App\Models\Product\Category;
use App\Http\Controllers\WebController;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class CatalogController
 */
class CatalogController extends WebController
{
    use SEOTools;
    /**
     * @return Factory|View
     */
    public function showAll()
    {
        $categories = Category::search('*')->get()->slice(0, 10);
        $categories = count($categories) > 0 ? $categories : Category::limit(10)->get();

        return view('catalog.catalog', ['categories' => $categories]);
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $this->seo()
            ->setTitle('Dropwow')
            ->setDescription('Dropwow allows you to find and sell top dropshipping products with fast shipping from US and China.');


        $this->seo()->opengraph()
            ->setTitle('Dropwow Dropshipping App')
            ->setUrl(route('catalog'))
            ->addImage('http://dropwow.com/img/dropwow.png')
            ->setType('website');
        $this->seo()->metatags()->setKeywords(['dropwow', 'dropshipping', 'shopify dropshipping app', 'fast shipping products', 'top selling items', 'find and sell products']);



        if (!auth()->check()) {
            return cache()->remember('view.maincatalog', 30, function () {
                $categories = Category::select(['id', 'title'])->orderByRaw('sort IS NULL, sort ASC, parent_id ASC')->take(5)->get()
                ->map(function (Category $category) {
                    $data = $category->toArray();
                    $id = $category->id;
                    $data['products'] = Product::search('*')
                        ->whereRegexp('categoriesPath.keyword', sprintf('.*/%d\/.+|%d\/.+|.*/%d', $id, $id, $id))
                        ->orderBy('id', 'DESC')
                        ->take(4)->get();
                    return $data;
                });

                return view('catalog.catalog', [
                        'categories' => $categories,
                        'menuCategories' => Category::mainCategories()
                    ])->render();
            });
        } else {
            $categories = Category::select(['id', 'title'])->orderByRaw('sort IS NULL, sort ASC, parent_id ASC')->take(5)->get()
                ->map(function (Category $category) {
                    $data = $category->toArray();
                    $id = $category->id;
                    $data['products'] = Product::search('*')
                        ->whereRegexp('categoriesPath.keyword', sprintf('.*/%d\/.+|%d\/.+|.*/%d', $id, $id, $id))
                        ->orderBy('id', 'DESC')
                        ->take(4)->get();
                    return $data;
                });

            return view('catalog.catalog', [
                'categories' => $categories,
                'menuCategories' => Category::mainCategories()
            ]);
        }
    }
}
