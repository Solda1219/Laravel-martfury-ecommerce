@php
    $crossSellProducts = [];
@endphp

<div class="ps-section--shopping ps-shopping-cart pt-40">
    <div class="container">
        <div class="ps-section__header">
            <h1>{{ __('Shopping Cart') }}</h1>
        </div>
        <div class="ps-section__content">

            @if (session()->has('success_msg'))
                <div class="alert alert-success mb-40">
                    <span>{{ session('success_msg') }}</span>
                </div>
            @endif

            @if (session()->has('error_msg'))
                <div class="alert alert-warning mb-40">
                    <span>{{ session('error_msg') }}</span>
                </div>
            @endif

            @if (isset($errors) && count($errors->all()) > 0)
                <div class="alert alert-warning mb-40">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                        @if (!$loop->last)
                            <br>
                        @endif
                    @endforeach
                </div>
            @endif

            <form class="form--shopping-cart" method="post" action="{{ route('public.cart.update') }}">
                @csrf
                    @if (Cart::instance('cart')->count() > 0)
                        @php
                            $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();

                            if ($productIds) {
                                $products = get_products([
                                    'condition' => [
                                        ['ec_products.id', 'IN', $productIds],
                                    ],
                                ]);
                            }
                        @endphp
                            <div class="table-responsive">
                                <table class="table ps-table--shopping-cart">
                                    <thead>
                                    <tr>
                                        <th>{{ __("Product's name") }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($products) && $products)
                                            @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                                                @php
                                                    $product = $products->where('id', $cartItem->id)->first();
                                                    if (!empty($product)) {
                                                        $crossSellProducts = array_unique(array_merge($crossSellProducts, get_cross_sale_products($product->original_product)));
                                                    }
                                                @endphp

                                                @if (!empty($product))
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">
                                                            <div class="ps-product--cart">
                                                                <div class="ps-product__thumbnail">
                                                                    <a href="{{ $product->original_product->url }}">
                                                                        <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" />
                                                                    </a>
                                                                </div>
                                                                <div class="ps-product__content"><a href="{{ $product->original_product->url }}">{{ $product->name }}</a>
                                                                    <p class="mb-0"><small>{{ $cartItem->options['attributes'] ?? '' }}</small></p>
                                                                    @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                        @foreach($cartItem->options['extras'] as $option)
                                                                            @if (!empty($option['key']) && !empty($option['value']))
                                                                                <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="price">{{ format_price($cartItem->price) }}</td>
                                                        <td>
                                                            <div class="form-group--number product__qty">
                                                                <button class="up">+</button>
                                                                <button class="down">-</button>
                                                                <input type="text" class="form-control qty-input" value="{{ $cartItem->qty }}" title="{{ __('Qty') }}" name="items[{{ $key }}][values][qty]" readonly>
                                                            </div>
                                                        </td>
                                                        <td>{{ format_price($cartItem->price * $cartItem->qty) }}</td>
                                                        <td><a href="{{ route('public.cart.remove', $cartItem->rowId) }}" class="remove-cart-button"><i class="icon-cross"></i></a></td>
                                                    </tr>
                                                    @endif
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                    @else
                        <p class="text-center">{{ __('Your cart is empty!') }}</p>
                    @endif

                    @if (Cart::instance('cart')->count() > 0)
                        <div class="ps-section__cart-actions">
                            <a class="ps-btn" href="{{ route('public.products') }}">
                                <i class="icon-arrow-left"></i> {{ __('Back to Shop') }}</a>
                            <button class="ps-btn ps-btn--outline" type="submit">
                                <i class="icon-sync"></i> {{ __('Update cart') }}</button>
                        </div>
                    @endif
                </form>
        </div>
        @if (Cart::instance('cart')->count() > 0)
            <div class="ps-section__footer">
                <div class="row">
                    <div class="col-lg-6 col-md-12 form-coupon-wrapper">
                        <figure>
                            <figcaption>{{ __('Coupon Discount') }}</figcaption>
                            <div class="form-group">
                                <input class="form-control coupon-code" type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="{{ __('Enter coupon code') }}">
                            </div>
                            <div class="form-group">
                                <button class="ps-btn ps-btn--outline btn-apply-coupon-code" type="button" data-url="{{ route('public.coupon.apply') }}">{{ __('Apply') }}</button>
                            </div>
                        </figure>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 ">
                        <div class="ps-block--shopping-total">
                            <div class="ps-block__header">
                                <p>{{ __('Subtotal') }} <span> {{ format_price(Cart::instance('cart')->rawSubTotal()) }}</span></p>
                            </div>
                            @if (EcommerceHelper::isTaxEnabled())
                                <div class="ps-block__header">
                                    <p>{{ __('Tax') }} <span> {{ format_price(Cart::instance('cart')->rawTax()) }}</span></p>
                                </div>
                            @endif
                            @if ($couponDiscountAmount > 0 && session('applied_coupon_code'))
                                <div class="ps-block__header">
                                    <p>{{ __('Coupon code: :code', ['code' => session('applied_coupon_code')]) }} (<small><a class="btn-remove-coupon-code text-danger" data-url="{{ route('public.coupon.remove') }}" href="javascript:void(0)" data-processing-text="{{ __('Removing...') }}">{{ __('Remove') }}</a></small>)<span> {{ format_price($couponDiscountAmount) }}</span></p>
                                </div>
                            @endif
                            @if ($promotionDiscountAmount)
                                <div class="ps-block__header">
                                    <p>{{ __('Discount promotion') }} <span> {{ format_price($promotionDiscountAmount) }}</span></p>
                                </div>
                            @endif
                            <div class="ps-block__content">
                                <h3>{{ __('Total') }} <span>{{ ($promotionDiscountAmount + $couponDiscountAmount) > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount) }}</span></h3>
                                <p><small>({{ __('Shipping fees not included') }})</small></p>
                            </div>
                        </div><a class="ps-btn ps-btn--fullwidth" href="{{ route('public.checkout.information', OrderHelper::getOrderSessionToken()) }}">{{ __('Proceed to checkout') }}</a>
                    </div>
                </div>
            </div>
        @endif

        @if (Cart::instance('cart')->count() > 0)
            @php
                $crossSellProducts = array_slice($crossSellProducts, 0, 7);
            @endphp
            @if (count($crossSellProducts) > 0)
                <div class="ps-section--default ps-customer-bought mt-60">
                    <div class="ps-section__header text-left pb-0" style="margin-bottom: 20px">
                        <h3 style="margin-bottom: 10px">{{ __('Customers who bought this item also bought') }}</h3>
                    </div>
                    <div class="ps-section__content">
                        <div class="row">
                            @foreach($crossSellProducts as $crossId)
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                                    <div class="ps-product">
                                        {!! Theme::partial('product-item', ['product' => get_product_by_id($crossId)]) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
