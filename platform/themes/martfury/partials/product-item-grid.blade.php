@if ($product)
    <div class="ps-product__thumbnail">
        <a href="{{ $product->url }}">
            <img src="{{ RvMedia::getImageUrl($product->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
        </a>
    </div>
    <div class="ps-product__container">
        <div class="ps-product__content">
            <a class="ps-product__title" href="{{ $product->url }}">{{ $product->name }}</a>
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
            <div class="ps-product__desc">
                {!! clean($product->description) !!}
            </div>
        </div>
        <div class="ps-product__shopping">
            <p class="ps-product__price @if ($product->front_sale_price !== $product->price) sale @endif">{{ format_price($product->front_sale_price) }} @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</p>
            @if (EcommerceHelper::isCartEnabled())
                <a class="ps-btn add-to-cart-button" data-id="{{ $product->id }}" href="{{ route('public.cart.add-to-cart') }}">{{ __('Add to cart') }}</a>
            @endif
            <ul class="ps-product__actions">
                <li><a class="js-add-to-wishlist-button" href="{{ route('public.wishlist.add', $product->id) }}"><i class="icon-heart"></i> {{ __('Wishlist') }}</a></li>
                <li><a class="js-add-to-compare-button" href="{{ route('public.compare.add', $product->id) }}"><i class="icon-chart-bars"></i>
                    {{ __('Compare') }}</a></li>
            </ul>
        </div>
    </div>
@endif
