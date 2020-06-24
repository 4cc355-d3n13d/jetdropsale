@section('product_category')
<div class="category">
    <div class="category__header">
        <h1 class="category__title">{{$category->title}}</h1>
        {{--
        <div class="category__tabs">
            <a href="#" title="" class="category__tabs_item is-active"><i class="fas fa-award"></i>Bestsellers</a>
            <a href="#" title="" class="category__tabs_item"><i class="fas fa-chart-bar"></i>Trending products</a>
            <a href="#" title="" class="category__tabs_item"><i class="fas fa-flag"></i>New items</a>
        </div>
        --}}
    </div>
    <div class="category__body">
        <div>
            @include('components.mobile-category-action-bar')
        </div>

        <div class="category__sidebar">


            @include('components.category-menu')
        </div>
        <div class="category__content">
            {{--@include('components.category-bestsellers')--}}
            @include('components.category-all')

        </div>

    </div>
    @include('components.pager')
</div>
@endsection
