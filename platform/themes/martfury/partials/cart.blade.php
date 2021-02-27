<div class="ps-cart__content">
    @if (Cart::instance('cart')->count() > 0)
        <div class="ps-cart__items">
            <div class="ps-cart__items__body">
                @php
                    $products = [];
                    $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();

                    if ($productIds) {
                        $products = get_products([
                            'condition' => [
                                ['ec_products.id', 'IN', $productIds],
                            ],
                            'with' => ['slugable'],
                        ]);
                    }
                @endphp
                @if (count($products))
                    @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                        @php
                            $product = $products->where('id', $cartItem->id)->first();
                        @endphp

                        @if (!empty($product))
                            <div class="ps-product--cart-mobile">
                                <div class="ps-product__thumbnail">
                                    <a href="{{ $product->original_product->url }}"><img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" /></a>
                                </div>
                                <div class="ps-product__content">
                                    <a class="ps-product__remove remove-cart-item" href="{{ route('public.cart.remove', $cartItem->rowId) }}"><i class="icon-cross"></i></a>
                                    <a href="{{ $product->original_product->url }}"> {{ $product->name }}</a>
                                    <p class="mb-0"><small>{{ $cartItem->qty }} x {{ format_price($cartItem->price) }}</small></p>
                                    <p class="mb-0"><small><small>{{ $cartItem->options['attributes'] ?? '' }}</small></small></p>
                                    @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                        @foreach($cartItem->options['extras'] as $option)
                                            @if (!empty($option['key']) && !empty($option['value']))
                                                <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <div class="ps-cart__footer">
            @if (EcommerceHelper::isTaxEnabled())
                <h5>{{ __('Sub Total') }}:<strong>{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</strong></h5>
                <h5>{{ __('Tax') }}:<strong>{{ format_price(Cart::instance('cart')->rawTax()) }}</strong></h5>
                <h3>{{ __('Total') }}:<strong>{{ format_price(Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax()) }}</strong></h3>
            @else
                <h3>{{ __('Sub Total') }}:<strong>{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</strong></h3>
            @endif
            <figure>
                <a class="ps-btn" href="{{ route('public.cart') }}">{{ __('View Cart') }}</a>
                @if (session('tracked_start_checkout'))
                    <a href="{{ route('public.checkout.information', session('tracked_start_checkout')) }}" class="ps-btn">{{ __('Checkout') }}</a>
                @endif
            </figure>
        </div>
    @else
        <div class="ps-cart__items ps-cart_no_items">
            <span class="cart-empty-message">{{ __('No products in the cart.') }}</span>
        </div>
    @endif
</div>
