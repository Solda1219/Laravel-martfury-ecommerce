<div class="container mb-80">
    <div class="row">
        <div class="col-sm-12">
            <article class="post-8">
                <!-- ==================== start cart page =================== -->
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <div class="container mb-80">
                    <div class="row">
                        <div class="col-sm-12">
                            <article class="post-8">
                                @if (session()->has('success_msg'))
                                    <div class="alert alert-success">
                                        <span>{{ session('success_msg') }}</span>
                                    </div>
                                @endif

                                @if (session()->has('error_msg'))
                                    <div class="alert alert-warning">
                                        <span>{{ session('error_msg') }}</span>
                                    </div>
                                @endif

                                @if (isset($errors) && count($errors->all()) > 0)
                                    <div class="alert alert-warning">
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                @if (Cart::instance('cart')->count() > 0)
                                    @php
                                        $crossSellProducts = [];

                                        Theme::set('body_class', 'shopping-cart');

                                        $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();

                                        if ($productIds) {
                                            $products = get_products([
                                                'condition' => [
                                                    ['ec_products.id', 'IN', $productIds],
                                                ],
                                            ]);
                                        }
                                    @endphp

                                    <form class="cart-form" method="post" action="{{ route('public.cart.update') }}">
                                        {!! csrf_field() !!}
                                        <div class="cart-product-table-wrap responsive-table">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th class="product-remove"></th>
                                                    <th class="product-thumbnail"></th>
                                                    <th class="product-name">{{ __('Product') }}</th>
                                                    <th class="product-price">{{ __('Price') }}</th>
                                                    <th class="product-quantity">{{ __('Quantity') }}</th>
                                                    <th class="product-subtotal">{{ __('Total') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if (isset($products) && $products)

                                                    @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                                                        @php
                                                            $product = $products->where('id', $cartItem->id)->first();
                                                            if(!empty($product)) {
                                                                //get parent product to get cross sell
                                                                $configurableProduct = get_parent_product($product->id);

                                                                if (!empty($configurableProduct)) {
                                                                    $crossSellProducts = array_unique(array_merge($crossSellProducts, get_cross_sale_products($configurableProduct)));
                                                                }
                                                            }
                                                        @endphp

                                                        @if(!empty($product))
                                                        <tr>
                                                            <td data-title="{{ trans('plugins/ecommerce::products.delete') }}">
                                                                <a title="{{ __('Delete product') }}" class="remove text-danger" href="{{ route('public.cart.remove', $cartItem->rowId) }}">
                                                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                                </a>

                                                            </td>
                                                            <td class="product-thumbnail">
                                                                <a href="{{ $product->url }}">
                                                                    <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" />
                                                                </a>
                                                            </td>

                                                            <td class="product-name" data-title="{{ __('Product Name') }}">
                                                                <a href="{{ $product->url }}">{{ $product->name }}</a>
                                                                <p style="margin-bottom: 0">
                                                                    <span style="display: block;font-style: italic;color:#555555; font-size: .9em;">{{ $cartItem->options['attributes'] ?? '' }}</span>
                                                                </p>
                                                                @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                    @foreach($cartItem->options['extras'] as $option)
                                                                        @if (!empty($option['key']) && !empty($option['value']))
                                                                            <p style="margin-bottom: 0;"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td class="product-price" data-title="{{ __('Unit Price') }}">
                                                                <span class="product-price-amount amount"><span class="currency-sign">
                                                                    {{ format_price($cartItem->price) }}
                                                                    </span>
                                                                        <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">
                                                                </span>
                                                            </td>
                                                            <td class="product-quantity" data-title="{{ __('Qty') }}">
                                                                <div class="product-quantity">
                                                                    <span data-value="+" class="quantity-btn quantityPlus"></span>
                                                                    <input class="quantity input-lg" step="1" min="1" max="9" title="{{ __('Qty') }}" value="{{ $cartItem->qty }}" name="items[{{ $key }}][values][qty]" type="number" />
                                                                    <span data-value="-" class="quantity-btn quantityMinus"></span>
                                                                </div>
                                                            </td>
                                                            <td class="product-subtotal" data-title="{{ __('Subtotal') }}">
                                                                <span class="amount">{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>


                                        <div class="row cart-actions">
                                            <div class="col-md-6">
                                                <div class="coupon">
                                                    @include('plugins/ecommerce::themes.discounts.partials.form')
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="submit" class="btn btn-md btn-gray">{{ __('Update cart') }}</button>
                                                <button type="submit" id="checkout" class="button-default" name="checkout">{{ __('Checkout') }}</button>
                                            </div>
                                        </div>

                                    </form>
                                    <div class="cart-collateral">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="relate-product-block row">
                                                    <h3 style="text-align: center; width: 100%;">{{ __('Cross-selling products') }}</h3>
                                                    @php
                                                        $crossSellProducts = array_slice($crossSellProducts, 0, 4);
                                                    @endphp
                                                    @if (!empty($crossSellProducts))
                                                        <div class="container product-carousel" style="margin-top: 10px;">
                                                            <div id="new-tranding" class="product-item-4 owl-carousel owl-theme nf-carousel-theme">
                                                                @foreach ($crossSellProducts as $crossId)
                                                                    {!! Theme::partial('product.product_simple', ['product' => get_product_by_id($crossId)]) !!}
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- End Product Carousel -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="cart_totals" id="main-checkout-product-info">
                                                    <h3>{{ __('Total') }}</h3>
                                                    <div class="responsive-table">
                                                        <table>
                                                            <tbody>
                                                            <tr class="cart-subtotal">
                                                                <th>{{ __('Subtotal') }}</th>
                                                                <td><span class="product-price-amount amount sub-total-text">
                                                                    {{ format_price(Cart::instance('cart')->rawSubTotal()) }}
                                                                    </span></td>
                                                            </tr>

                                                            <tr class="coupon-information @if (session('applied_coupon_code') == null) hidden @endif">
                                                                <th>{{ __('Coupon code') }}</th>
                                                                <td><span class="coupon-code-text">{{ session('applied_coupon_code') }}</span></td>
                                                            </tr>

                                                            <tr class="discount-amount @if ($couponDiscountAmount == 0) hidden @endif">
                                                                <th>{{ __('Discount') }}</th>
                                                                <td><span class="total-discount-amount-text">{{ format_price($couponDiscountAmount) }}</span></td>
                                                            </tr>

                                                            @if ($promotionDiscountAmount)
                                                                <tr class="discount-amount">
                                                                    <th>{{ __('Discount promotion') }}</th>
                                                                    <td><span class="promotion-discount-amount-text">{{ format_price($promotionDiscountAmount) }}</span></td>
                                                                </tr>
                                                            @endif

                                                            <tr class="order-total">
                                                                <th>{{ __('Total') }}</th>
                                                                <td><span class="product-price-amount amount raw-total-text">
                                                                {{ format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount) }}
                                                            </span></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </article>
                        </div>
                    </div>
                </div>
                <!-- ==================== //end cart page =================== -->
            </article>
        </div>
    </div>
</div>
<!-- End Content Page -->
