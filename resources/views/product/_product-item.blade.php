@php
    $options = $product->options->groupBy('name')->toArray();
@endphp

@section('breadcrumbs', Breadcrumbs::render('product', $product->category, $product))

<div class="product" ref="mainDiv" data-product-id="{{ $product->id }}">
    <div class="product__preview">
        <div class="product__preview_img">
            <img ref="productMainImage" src="{{ $product->image }}" alt="">
        </div>
        <div class="product__preview_nav">
                @php
                    $images = json_decode($product->images);
                @endphp
                <images-carousel
                    :max="4"
                    :images="{{json_encode($images)}}"
                    :click="onImageClick"
                    :main="mainImgIndex"
                />

        </div>
    </div>
    <div id="product_description" class="product__description">
        <div class="product__description_header">
            <h3 class="product__title">{{ $product->title }}</h3>


            {{--
            <div class="product__feedback">
                <div class="product__raiting">
                    <span class="product__raiting_item"><i class="fas fa-star"></i></span>
                    <span class="product__raiting_item"><i class="fas fa-star"></i></span>
                    <span class="product__raiting_item"><i class="fas fa-star"></i></span>
                    <span class="product__raiting_item"><i class="fas fa-star"></i></span>
                    <span class="product__raiting_item"><i class="far fa-star"></i></span>
                </div>
                <div class="product__reviews"><i class="fas fa-comment-alt"></i>Reviews: 12</div>
            </div>
            --}}

            <div ref="price" class="product__cost" v-text="'$ ' + (price || {{$product->price}})"></div>
            @can('viewAliLink')
            <div><a href="{{ $product->getAliLink() }}" target="_blank" class="btn btn-primary">Ali: {{ $product->ali_id }}</a> </div>
            @endcan


        </div>
        <div class="product__description_body">
            @if(count($options) > 0)
                <div class="product__variants">
                <div class="product__variants_header">
                    <h4 class="product__variants_title">Variants</h4>
                    {{--<a href="#" title="" class="product__variants_more">All variants</a>--}}
                </div>

                @foreach ($options as $name => $option)
                    <div class="product__variants-type">
                        <div class="product__variants-type_label">{{ $name }}:</div>
                        <ul class="product__variants-type_value">
                            @foreach ($option as $key => $optionDetails)
                                <li
                                        @click="onOptionClick"
                                        :class="{'product__variants-type_item': true, 'is-active': options[{{$optionDetails['ali_option_id']}}] == {{ $optionDetails['ali_sku'] }}}"
                                        data-option_id="{{ $optionDetails['ali_option_id'] }}"
                                        data-sku="{{ $optionDetails['ali_sku'] }}">
                                    @if($optionDetails['image'])
                                        <img src="{{ $optionDetails['image'] }}" alt="{{ $optionDetails['value'] }}" title="{{ $optionDetails['value'] }}">
                                    @else
                                       <div> {{ $optionDetails['value'] }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            @endif
            <div class="product__info">
                <div class="product__info_label">Shipping:</div>
                <div>
                    <div class="product__info_value">Processing time 1-5 days</div>
                    @if ($product->getShipcountriesAttribute())
                        @foreach ($product->getShipcountriesAttribute() as $key => $shipCountry)
                            @if ($shipCountry !== 'China')
                                <div class="product__info_value">Shipping time from {{$shipCountry}} to {{$shipCountry}} 4-12 days</div>
                            @endif
                        @endforeach
                    @endif
                    <div class="product__info_value">Shipping time from China to United States / Europe 7-14 days</div>
                    <div class="product__info_value">Shipping time from China to WW 10-30 days</div>
                    <div class="product__info_value">Shipping cost ${{$shippingPrice}} per order</div>
                </div>
            </div>
            <div class="product__info">
                <div class="product__info_label">Availability:</div>
                <div ref="optionAmount" class="product__info_value">{{ $product->amount }}</div>
            </div>
            {{--
            <div class="product__info">
                <div class="product__info_label">Quantity:</div>
                <div class="product__info_value">
                    @include('components._counter')
                </div>
            </div>
            --}}
        </div>
        @guest
            <div class="product__description_footer-item">
                <a href="/login" id="{{ $product['id'] }}">
                    <span class="btn btn-fill"><i class="fas fa-plus-square"></i>Add to my products</span>
                </a>{{--<span class="info"><i class="fas fa-info-circle"></i></span>--}}
            </div>
        @else
        <div @click="()=>onProductCardImport({{ $product->id }})" class="product__description_footer-item">
            <span class="btn btn-fill"><i class="fas fa-plus-square"></i>Add to my products</span>{{--<span class="info"><i class="fas fa-info-circle"></i></span>--}}
        </div>
        @endguest
        {{--
        <div class="product__description_footer-item">
            <span class="btn btn-link">Buy sample</span><span class="info"><i class="fas fa-info-circle"></i></span>
        </div>
        --}}
    </div>
</div>
