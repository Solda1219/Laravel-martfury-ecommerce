@php
    $originalProduct = $product;
    $selectedAttrs = [];
    $productImages = $product->images;
    if ($product->is_variation) {
        $product = get_parent_product($product->id);
        $selectedAttrs = app(\Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface::class)
            ->getAttributeIdsOfChildrenProduct($originalProduct->id);
        if (count($productImages) == 0) {
            $productImages = $product->images;
        }
    } else {
        $selectedAttrs = $product->defaultVariation->productAttributes->pluck('id')->all();
    }

    Theme::asset()->remove('app-js');

@endphp

<div class="ps-product__header">
    <div class="ps-product__thumbnail" data-vertical="false">
        <div class="ps-product__images" data-arrow="true">
            @foreach ($productImages as $img)
                <div class="item"><img src="{{ RvMedia::getImageUrl($img) }}" alt="{{ $product->name }}"></div>
            @endforeach
        </div>
    </div>
    <div class="ps-product__info">
        <h1><a href="{{ $product->url }}">{{ $product->name }}</a></h1>
        <div class="ps-product__meta">
            <p>{{ __('Brand') }}: <a href="{{ $product->brand->url }}">{{ $product->brand->name }}</a></p>
            @if (EcommerceHelper::isReviewEnabled())
                @php $countRating = get_count_reviewed_of_product($product->id) @endphp
                @if ($countRating > 0)
                    <div class="rating_wrap">
                        <div class="rating">
                            <div class="product_rate" style="width: {{ get_average_star_of_product($product->id) * 20 }}%"></div>
                        </div>
                        <span class="rating_num">({{ $countRating }} {{ __('reviews') }})</span>
                    </div>
                @endif
            @endif
        </div>
        <h4 class="ps-product__price @if ($product->front_sale_price !== $product->price) sale @endif">{{ format_price($product->front_sale_price) }} @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</h4>
        <div class="ps-product__desc">
            <div class="ps-list--dot">
                {!! clean($product->description) !!}
            </div>
        </div>
        @if ($product->variations()->count() > 0)
            <div class="pr_switch_wrap">
                {!! render_product_swatches($product, [
                    'selected' => $selectedAttrs,
                    'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                ]) !!}
            </div>
            <div class="number-items-available" style="display: none; margin-bottom: 10px;"></div>
        @endif
        <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
            @csrf
            <div class="ps-product__shopping">
                <input type="hidden" name="id" class="hidden-product-id" value="{{ ($originalProduct->is_variation || !$originalProduct->defaultVariation->product_id) ? $originalProduct->id : $originalProduct->defaultVariation->product_id }}"/>
                <input type="hidden" name="qty" value="1">
                @if (EcommerceHelper::isCartEnabled())
                    <button class="ps-btn ps-btn--black" type="submit">{{ __('Add to cart') }}</button>
                    @if (EcommerceHelper::isQuickBuyButtonEnabled())
                        <button class="ps-btn" type="submit" name="checkout">{{ __('Buy Now') }}</button>
                    @endif
                @endif
                <div class="ps-product__actions">
                    <a class="js-add-to-wishlist-button" href="{{ route('public.wishlist.add', $product->id) }}"><i class="icon-heart"></i></a>
                    <a class="js-add-to-compare-button" href="{{ route('public.compare.add', $product->id) }}"><i class="icon-chart-bars"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>
