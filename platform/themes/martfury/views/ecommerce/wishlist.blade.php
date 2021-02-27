<div class="ps-section--shopping pt-40">
    <div class="container">
        <div class="ps-section__header">
            <h1>{{ __('Wishlist') }}</h1>
        </div>
        <div class="ps-section__content">
            @if ((auth('customer')->check() && count($wishlist) > 0 && $wishlist->count() > 0) || Cart::instance('wishlist')->count())
                <div class="table-responsive">
                <table class="table ps-table--whishlist ps-table--responsive">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="text-left">{{ __('Image') }}</th>
                        <th class="text-left">{{ __('Price') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (auth('customer')->check())
                            @foreach ($wishlist as $item)
                                @php $product = $item->product; @endphp
                                <tr>
                                    <td data-label="{{ __('Remove') }}">&nbsp;<a class="js-remove-from-wishlist-button" href="{{ route('public.wishlist.remove', $product->id) }}"><i class="icon-cross"></i></a></td>
                                    <td data-label="{{ __('Product') }}">
                                        <div class="ps-product--cart">
                                            <div class="ps-product__thumbnail"><a href="{{ $product->original_product->url }}"><img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"></a></div>
                                            <div class="ps-product__content">
                                                <a href="{{ $product->original_product->url }}">{{ $product->name }}</a>
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
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price" data-label="{{ __('Price') }}"><span>{{ format_price($product->front_sale_price) }}</span> @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</td>
                                    @if (EcommerceHelper::isCartEnabled())
                                        <td data-label="{{ __('Action') }}"><a class="ps-btn add-to-cart-button" data-id="{{ $product->id }}" href="{{ route('public.cart.add-to-cart') }}">{{ __('Add to cart') }}</a></td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            @foreach(Cart::instance('wishlist')->content() as $cartItem)
                                @php
                                    $product = app(\Botble\Ecommerce\Repositories\Interfaces\ProductInterface::class)->findById($cartItem->id);
                                @endphp
                                @if (!empty($product))
                                    <tr>
                                        <td data-label="{{ __('Remove') }}">&nbsp;<a class="js-remove-from-wishlist-button" href="{{ route('public.wishlist.remove', $product->id) }}"><i class="icon-cross"></i></a></td>
                                        <td data-label="{{ __('Product') }}">
                                            <div class="ps-product--cart">
                                                <div class="ps-product__thumbnail"><a href="{{ $product->original_product->url }}"><img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"></a></div>
                                                <div class="ps-product__content">
                                                    <a href="{{ $product->original_product->url }}">{{ $product->name }}</a>
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
                                                </div>
                                            </div>
                                        </td>
                                        <td class="price" data-label="{{ __('Price') }}"><span>{{ format_price($product->front_sale_price) }}</span> @if ($product->front_sale_price !== $product->price) <del>{{ format_price($product->price) }} </del> @endif</td>
                                        @if (EcommerceHelper::isCartEnabled())
                                            <td data-label="{{ __('Action') }}"><a class="ps-btn add-to-cart-button" data-id="{{ $product->id }}" href="{{ route('public.cart.add-to-cart') }}">{{ __('Add to cart') }}</a></td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>

                @if (auth('customer')->check())
                    <div class="ps-pagination">
                        {!! $wishlist->links() !!}
                    </div>
                @endif
            </div>
            @else
                <p class="text-center">{{ __('No product in wishlist!') }}</p>
            @endif
        </div>
    </div>
</div>
