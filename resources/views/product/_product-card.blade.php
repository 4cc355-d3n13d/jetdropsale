@if($product)
    <li class="catalogue__item">
        <span class="catalogue__item_img"><a href="{{ route('product.show', $product) }}"><img
                        src="{{ $product['image'] }}" alt=""></a></span>
        <span class="catalogue__item_title" title="{{ $product['title'] }}">
                <a href="{{ route('product.show', $product['id']) }}">{{ $product['title'] }}</a>
            </span>
        <div class="catalogue__item_info">
            <span class="catalogue__item_cost">$ {{ $product['price'] }}</span>
        </div>
        <span class="catalogue__item_btn">
            @guest
                <span @click="showGuestPopup" class="btn btn-wide"
                      id="{{ $product['id'] }}">Add to my products</span>
            @else
                <span @click="onAddToImportListClick" class="btn btn-wide"
                      id="{{ $product['id'] }}">Add to my products</span>
            @endguest
            </span>
    </li>
@endif
