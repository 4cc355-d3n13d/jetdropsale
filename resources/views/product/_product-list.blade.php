@section('content')
    @include('components._content-menu')
    <div class="category__content">
        <div class="catalogue__category">
            <h3 class="catalogue__title">Search: {{ request('query') }}</h3>
            <ul class="catalogue__list">
                @each('product._product-card', $products, 'product')
            </ul>
            <div>{{$products->appends($_GET)->links()}}</div>
        </div>
    </div>
@endsection

