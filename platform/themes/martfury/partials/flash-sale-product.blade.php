<div class="ps-product__thumbnail"><a href="{{ $product->url }}"><img src="{{ RvMedia::getImageUrl($product->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"></a>
    @if ($product->front_sale_price !== $product->price)
        <div class="ps-product__badge">{{ get_sale_percentage($product->price, $product->front_sale_price) }}</div>
    @endif
    <ul class="ps-product__actions">
        @if (EcommerceHelper::isCartEnabled())
            <li><a class="add-to-cart-button" data-id="{{ $product->id }}" href="{{ route('public.cart.add-to-cart') }}" title="{{ __('Add To Cart') }}"><i class="icon-bag2"></i></a></li>
        @endif
        <li><a href="{{ route('public.ajax.quick-view', $product->id) }}" title="{{ __('Quick View') }}" class="js-quick-view-button"><i class="icon-eye"></i></a></li>
        <li><a class="js-add-to-wishlist-button" href="{{ route('public.wishlist.add', $product->id) }}" title="{{ __('Add to Wishlist') }}"><i class="icon-heart"></i></a></li>
        <li><a class="js-add-to-compare-button" href="{{ route('public.compare.add', $product->id) }}" title="{{ __('Compare') }}"><i class="icon-chart-bars"></i></a></li>
    </ul>
</div>
<div class="ps-product__container">
    <p class="ps-product__price @if ($product->front_sale_price !== $product->price) sale @endif">{{ format_price($product->front_sale_price) }} @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</p>
    <div class="ps-product__content"><a class="ps-product__title" href="{{ $product->url }}">{{ $product->name }}</a>
        @if (EcommerceHelper::isReviewEnabled())
            @php $countRating = get_count_reviewed_of_product($product->id); @endphp
            @if ($countRating > 0)
                <div class="rating_wrap">
                    <div class="rating">
                        <div class="product_rate" style="width: {{ get_average_star_of_product($product->id) * 20 }}%"></div>
                    </div>
                    <span class="rating_num">({{ $countRating }})</span>
                </div>
            @endif
        @endif
        <div class="ps-product__progress-bar ps-progress" data-value="{{ $product->pivot->quantity > 0 ? ($product->pivot->sold / $product->pivot->quantity) * 100 : 0 }}">
            <div class="ps-progress__value"><span style="width: {{ $product->pivot->quantity > 0 ? ($product->pivot->sold / $product->pivot->quantity) * 100 : 0 }}%"></span></div>
            @if ($product->pivot->quantity > $product->pivot->sold)
                <p>{{ __('Sold') }}: {{ (int)$product->pivot->sold }}</p>
            @else
                <p class="text-danger">{{ __('Sold out') }}</p>
            @endif
        </div>
    </div>
</div>
