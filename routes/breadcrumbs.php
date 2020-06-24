<?php

use \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// Catalog
Breadcrumbs::for('catalog', function ($breadcrumbs) {
    $breadcrumbs->push('Catalog', route('catalog'));
});

// Search
Breadcrumbs::for('search', function ($breadcrumbs) {
    $breadcrumbs->parent('catalog');
    $breadcrumbs->push('Search');
});

// Catalog / Search / [searchRaw]
Breadcrumbs::for('catalog.search', function ($breadcrumbs, $search) {
    $breadcrumbs->parent('search');
    $breadcrumbs->push($search, route('catalog.search'));
});

// Category / [CategoryTitle] / [SubCategoryTitle] / [CurrentCategoryTitle]
Breadcrumbs::for('category', function ($breadcrumbs, $category) {
    if ($category->parent_id) {
        $breadcrumbs->parent('category', $category->parent);
    } else {
        $breadcrumbs->parent('catalog');
    }
    $breadcrumbs->push($category['title'], route('category.products', $category['id']));
});

// Category / [CategoryTitle] / [SubCategoryTitle] / [CurrentCategoryTitle] / [ProductTitle]
Breadcrumbs::for('product', function ($breadcrumbs, $category, $product) {
    $breadcrumbs->parent('category', $category);
    $breadcrumbs->push($product->title, route('product.show', $product));
});
