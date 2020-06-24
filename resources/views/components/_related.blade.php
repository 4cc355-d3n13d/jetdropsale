<div class="catalogue">

        <div class="catalogue__title-more" style="width: auto">
            <div class="catalogue__title-more__title">Similar products</div>
            <a href="{{ @route('category.products', ['category' => $product->category, 'query' => $product->title]) }}" class="catalogue__title-more__more">See more</a>
        </div>
        <div class="catalogue__list" style=" margin: -10px;">
            @each('product._product-card', $related, 'product')
        </div>

</div>
