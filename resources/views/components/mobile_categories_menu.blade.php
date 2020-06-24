{{--<div class="mobile-categories-menu">--}}
    {{--<div class="dropdown">--}}
        {{--@foreach($menuCategories as $category)--}}
            {{--<div class="dropdown-item">--}}
                {{--{{$category['title']}}--}}
            {{--</div>--}}
        {{--@endforeach--}}
    {{--</div>--}}
{{--</div>--}}

<mobile-categories-menu
    :categories="{{json_encode($menuCategories)}}"
>
</mobile-categories-menu>




{{--<div class="billboard-menu__item">--}}
    {{--<span class="billboard-menu__item_icon"><i class="fas {{ $category['icon'] }}"></i></span>--}}
    {{--<span class="billboard-menu__item_text">--}}
                    {{--@if(!$category['path'])--}}
            {{--{{ $category['title'] }}--}}
        {{--@else--}}
            {{--<a href="{{ $category['path'] }}">{{ $category['title'] }}</a>--}}
        {{--@endif--}}
                {{--</span>--}}
    {{--<span class="billboard-menu__item_icon"><i class="fas fa-chevron-right"></i></span>--}}
    {{--<div class="billboard-menu__item__subcategories">--}}
        {{--@foreach($category['children'] as $subcategory)--}}
            {{--<div class="billboard-menu__item__subcategories__subcategory">--}}
                {{--<div class="billboard-menu__item__subcategories__subcategory__title">--}}
                    {{--<a href="{{$subcategory['path']}}">{{$subcategory['title']}}</a>--}}
                {{--</div>--}}
                {{--<div class="billboard-menu__item__subcategories__subcategory__content">--}}
                    {{--@foreach($subcategory['children'] as $subcategoryTitle)--}}
                        {{--<div>--}}
                            {{--<a href="{{$subcategoryTitle['path']}}">{{$subcategoryTitle['title']}}</a>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--@endforeach--}}

    {{--</div>--}}
{{--</div>--}}