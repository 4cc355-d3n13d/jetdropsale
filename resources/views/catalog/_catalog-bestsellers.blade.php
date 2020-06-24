@foreach($categories as $category)
    @if(count($category->products) > 0)
    <div class="catalogue">
        <h3 class="catalogue__title">Bestsellers</h3>
        <ul class="catalogue__list">
            @each("product._product-card", $category->products, 'product')
        </ul>
        <a href="#" class="catalogue__more">See more</a>
    </div>
@endif
@endforeach