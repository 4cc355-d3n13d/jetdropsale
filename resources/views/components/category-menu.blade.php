<div class="category-menu">
    <div class="category_menu__title">
        Filters
    </div>
    @include('components.range')
    <div class="category_menu__title">
        Subcategories
    </div>
    @if($categories)
        @foreach($categories->toArray() as $subcategory)
            <a href="{{$subcategory['path']}}" class="category-menu__item">
                <span class="category-menu__item_text">{{$subcategory['title']}}</span>
                <span class="category-menu__item_icon"><i class="fas fa-chevron-right"></i></span>
            </a>
        @endforeach
    @endif
</div>