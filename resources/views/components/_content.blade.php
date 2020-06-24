<div class="content">
    <div class="tabs">
        <ul class="tabs__nav">
            <li class="tabs__nav_item is-active">Product details</li>
            {{--<li class="tabs__nav_item">Feedback 227</li>--}}
            {{--<li class="tabs__nav_item">Shipping & Payment</li>--}}
        </ul>
        <div class="tabs__content">
            @if($product->details()->count())
                <ul class="content__list">
                    @foreach($product->details as $detail)
                        <li class="content__list_item">
                            <span class="content__list_label">{{ $detail->title }}</span>
                            <span class="content__list_value">{{ $detail->value }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="content__text" :class="description_expanded ? '' : 'short'">
                <div class="description-content">
                    {!! $product->description !!}
                </div>
                <div @click="toggle" class="toggler-container">
                    <span class="toggler">@{{description_expanded ? 'Collapse' : 'See more'}}
                        <i v-if="description_expanded" class="fas fa-chevron-up"></i>
                    <i v-else class="fas fa-chevron-down"></i>
                    </span>

                </div>
            </div>
        </div>
    </div>
</div>