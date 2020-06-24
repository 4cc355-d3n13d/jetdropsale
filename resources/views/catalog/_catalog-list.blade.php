@foreach($categories as $category)
        <div class="catalogue">
            <div class="catalogue__title-more">
                <a href="{{ $category['path'] }}"><h3 class="catalogue__title-more__title">{{ $category['title'] }}</h3></a>
                @if(isset($category['path']))
                    <a href="{{ $category['path'] }}" class="catalogue__title-more__more">See more</a>
                @else
                    <a href="{{ route('category.products', $category['id']) }}" class="catalogue__title-more__more">See more</a>
                @endif
            </div>
        <ul class="catalogue__list catalogue__list__bestsellers">
            @each('product._product-card', $category['products'], 'product')
        </ul>

    </div>
@endforeach
