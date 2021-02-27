<header class="header header--product" data-sticky="true">
    <nav class="navigation">
        <div class="container">
            <article class="ps-product--header-sticky">
                <div class="ps-product__thumbnail">
                    <img src="{{ RvMedia::getImageUrl($product->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                </div>
                <div class="ps-product__wrapper">
                    <div class="ps-product__content"><a class="ps-product__title" href="{{ $product->url }}">{{ $product->name }}</a>
                        <ul class="ps-tab-list">
                            <li class="active"><a href="#tab-description">{{ __('Description') }}</a></li>
                            @if (EcommerceHelper::isReviewEnabled())
                                <li><a href="#tab-reviews">{{ __('Reviews') }} ({{ $countRating }})</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="ps-product__shopping">
                        <span class="ps-product__price">
                            <span>{{ format_price($product->front_sale_price) }}</span>
                            @if ($product->front_sale_price !== $product->price)
                                <del>{{ format_price($product->price) }}</del>
                            @endif
                        </span>
                        @if (EcommerceHelper::isCartEnabled())
                            <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                                @csrf
                                <input type="hidden" name="id" class="hidden-product-id" value="{{ ($product->is_variation || !$product->defaultVariation->product_id) ? $product->id : $product->defaultVariation->product_id }}"/>
                                <input type="hidden" name="qty" value="1">
                                <button class="ps-btn" type="submit">{{ __('Add to cart') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </nav>
</header>
