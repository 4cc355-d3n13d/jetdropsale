<div class="billboard">
    {{--<div id="carouselExampleControlsMid" class="carousel slide billboard__bestsellers__carousel-mid" data-ride="carousel">--}}
        {{--<div class="carousel-inner">--}}
            {{--<div class="carousel-item active">--}}
                {{--<a href="https://apps.shopify.com/dropwow/?utm_medium=internal&utm_source=dropwownew&utm_term=main_banner" target="_blank"><img class="d-block w-100" src="/img/big_1.png" alt="First slide"></a>--}}
            {{--</div>--}}
            {{--<div class="carousel-item">--}}
                {{--<a href="https://help.dropwow.com/" target="_blank"><img class="d-block w-100" hidden src="/img/big_2.png" alt="Second slide"></a>--}}
            {{--</div>--}}
            {{--<div class="carousel-item">--}}
                {{--<a href="{{ route('category.products', ['id'=>408]) }}"><img class="d-block w-100" hidden src="/img/big_3.png" alt="Third slide"></a>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<a class="billboard-tile__banner_btn is-left carousel-control-prev" data-slide="prev" role="button"><i class="fas fa-chevron-left carousel-control-prev-icon"></i></a>--}}
        {{--<a class="billboard-tile__banner_btn is-right carousel-control-next" data-slide="next" role="button"><i class="fas fa-chevron-right carousel-control-next-icon"></i></a>--}}
        {{--<span class="billboard-tile__banner_btn is-left" href="#carouselExampleControlsMid" role="button"--}}
              {{--data-slide="prev">--}}
            {{--<i class="fas fa-chevron-left"></i>--}}
                {{--<span class="carousel-control-prev-icon" aria-hidden="true"></span>--}}
                {{--<span class="sr-only">Previous</span>--}}
        {{--</span>--}}
        {{--<span class="billboard-tile__banner_btn is-right" href="#carouselExampleControlsMid" role="button"--}}
              {{--data-slide="next">--}}
            {{--<span class="carousel-control-next-icon" aria-hidden="true"></span>--}}
                {{--<i class="fas fa-chevron-right"></i>--}}
            {{--<span class="sr-only">Next</span>--}}
        {{--</span>--}}
    {{--</div>--}}

        <div class="billboard-content">
            <div class="billboard-menu">
                @foreach($menuCategories as $category)

                    <div class="billboard-menu__item">
                        <span class="billboard-menu__item_icon"><i class="fas {{ $category['icon'] }}"></i></span>
                        <span class="billboard-menu__item_text">
                    @if(!$category['path'])
                                {{ $category['title'] }}
                            @else
                                <a href="{{ $category['path'] }}">{{ $category['title'] }}</a>
                            @endif
                </span>
                        <span class="billboard-menu__item_icon"><i class="fas fa-chevron-right"></i></span>
                        <div class="billboard-menu__item__subcategories">
                            @foreach($category['children'] as $subcategory)
                                <div class="billboard-menu__item__subcategories__subcategory">
                                    <div class="billboard-menu__item__subcategories__subcategory__title">
                                        <a href="{{$subcategory['path']}}">{{$subcategory['title']}}</a>
                                    </div>
                                    <div class="billboard-menu__item__subcategories__subcategory__content">
                                        @foreach($subcategory['children'] as $subcategoryTitle)
                                            <div>
                                                <a href="{{$subcategoryTitle['path']}}">{{$subcategoryTitle['title']}}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                @endforeach
            </div>

            {{--<div class="billboard__bestsellers-mid">--}}
                {{--<div class="best-categories">--}}
                    {{--<a href="{{ route("category.products", ['id'=>18]) }}" class="best-category">--}}
                        {{--<div class="best-category__label">--}}
                            {{--Jewelry--}}
                        {{--</div>--}}
                        {{--<img src="img/category_1.png" alt="">--}}
                    {{--</a>--}}
                    {{--<a href="{{ route("category.products", ['id'=>82]) }}" class="best-category">--}}
                        {{--<div class="best-category__label">--}}
                            {{--Pet<br>--}}
                            {{--Products--}}
                        {{--</div>--}}
                        {{--<img src="img/category_2.png" alt="">--}}
                    {{--</a>--}}
                    {{--<a href="{{ route("category.products", ['id'=>5]) }}" class="best-category">--}}
                        {{--<div class="best-category__label">--}}
                            {{--Women's<br>--}}
                            {{--Clothing--}}
                        {{--</div>--}}
                        {{--<img src="img/category_3.png" alt="">--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="billboard__bestsellers">
                <div id="carouselExampleControls" class="carousel slide billboard__bestsellers__carousel" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <a href="https://apps.shopify.com/dropwow/?utm_medium=internal&utm_source=dropwownew&utm_term=main_banner" target="_blank"><img class="d-block w-100" src="/img/big_1.png" alt="First slide"></a>
                        </div>
                        <div class="carousel-item">
                            <a href="https://help.dropwow.com/" target="_blank"><img class="d-block w-100" hidden src="/img/big_2.png" alt="Second slide"></a>
                        </div>
                        <div class="carousel-item">
                            <a href="{{ route('category.products', ['id'=>9]) }}"><img class="d-block w-100" hidden src="/img/big_4.png" alt="Third slide"></a>
                        </div>
                    </div>
                    {{--<a class="billboard-tile__banner_btn is-left carousel-control-prev" data-slide="prev" role="button"><i class="fas fa-chevron-left carousel-control-prev-icon"></i></a>--}}
                    {{--<a class="billboard-tile__banner_btn is-right carousel-control-next" data-slide="next" role="button"><i class="fas fa-chevron-right carousel-control-next-icon"></i></a>--}}
                    <span class="billboard-tile__banner_btn is-left" href="#carouselExampleControls" role="button"
                          data-slide="prev">
            <i class="fas fa-chevron-left"></i>
                        {{--<span class="carousel-control-prev-icon" aria-hidden="true"></span>--}}
                        <span class="sr-only">Previous</span>
        </span>
                    <span class="billboard-tile__banner_btn is-right" href="#carouselExampleControls" role="button"
                          data-slide="next">
            {{--<span class="carousel-control-next-icon" aria-hidden="true"></span>--}}
                        <i class="fas fa-chevron-right"></i>
            <span class="sr-only">Next</span>
        </span>
                </div>
                <div class="best-categories">
                    <a href="{{ route("category.products", ['id'=>18]) }}" class="best-category">
                        <div class="best-category__label">
                            Jewelry
                        </div>
                        <img src="img/category_1.png" alt="">
                    </a>
                    <a href="{{ route("category.products", ['id'=>82]) }}" class="best-category">
                        <div class="best-category__label">
                            Pet<br>
                            Products
                        </div>
                        <img src="img/category_4.png" alt="">
                    </a>
                    <a href="{{ route("category.products", ['id'=>5]) }}" class="best-category">
                        <div class="best-category__label">
                            Women's<br>
                            Clothing
                        </div>
                        <img src="img/category_3.png" alt="">
                    </a>
                </div>
            </div>
        </div>

</div>
