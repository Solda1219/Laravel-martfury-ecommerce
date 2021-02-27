@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Checkout') }}
@stop
@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/libraries/card/card.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/payment/css/payment.css') }}?v=1.0.2">

    @if (Cart::instance('cart')->count() > 0)
        {!! Form::open(['route' => ['public.checkout.process', $token], 'class' => 'checkout-form payment-checkout-form', 'id' => 'checkout-form']) !!}
        <input type="hidden" name="checkout-token" id="checkout-token" value="{{ $token }}">
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

        <div class="row">
            <div class="col-lg-7 col-md-6 col-12 left">

                @if (theme_option('logo'))
                    <div class="checkout-logo">
                        <a href="{{ url('/') }}" title="{{ theme_option('site_title') }}">
                            <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="img-fluid" width="150" alt="{{ theme_option('site_title') }}" />
                        </a>
                    </div>
                    <hr/>
                @endif

                <!-- for mobile device display -->
                <div class="d-sm-block d-md-none" style="padding: 0 15px;" id="main-checkout-product-info-mobile">
                    <div class="payment-info-loading" style="display: none;">
                        <div class="payment-info-loading-content">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <div id="cart-item">
                        @if (isset($products) && $products)
                            <p>{{ __('Product(s)') }}:</p>
                            @foreach(Cart::instance('cart')->content() as $key => $cartItem)

                                @php
                                    $product = $products->where('id', $cartItem->id)->first();
                                @endphp

                                @if(!empty($product))
                                    <div class="row cart-item">
                                        <div class="col-3">
                                            <div class="checkout-product-img-wrapper">
                                                <img class="item-thumb img-thumbnail img-rounded" src="{{ $cartItem->options['image']}}" alt="{{ $product->name ?? '' }}">
                                                <span class="checkout-quantity">{{ $cartItem->qty }}</span>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <p style="margin-bottom: 0;">{{ $product->name }}</p>
                                            <p style="margin-bottom: 0">
                                                @php $attributes = get_product_attributes($product->id) @endphp
                                                @if (!empty($attributes))
                                                    <small>
                                                        @foreach ($attributes as $attr)
                                                            @if (!$loop->last)
                                                                {{ $attr->attribute_set_title }}: {{ $attr->title }},
                                                            @else
                                                                {{ $attr->attribute_set_title }}: {{ $attr->title }}
                                                            @endif
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </p>
                                            @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                @foreach($cartItem->options['extras'] as $option)
                                                    @if (!empty($option['key']) && !empty($option['value']))
                                                        <p style="margin-bottom: 0;"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                    @endif
                                                @endforeach
                                            @endif

                                        </div>
                                        <div class="col-4 float-right">
                                            <p>{{ format_price($cartItem->price) }}</p>
                                        </div>
                                    </div> <!--  /item -->
                                @endif
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-6">
                                <p>{{ __('Subtotal') }}:</p>
                            </div>
                            <div class="col-6">
                                <p class="price-text sub-total-text text-right"> {{ format_price(Cart::instance('cart')->rawSubTotal()) }} </p>
                            </div>
                        </div>
                        @if (session('applied_coupon_code'))
                            <div class="row coupon-information">
                                <div class="col-6">
                                    <p>{{ __('Coupon code') }}:</p>
                                </div>
                                <div class="col-6">
                                    <p class="price-text coupon-code-text"> {{ session('applied_coupon_code') }} </p>
                                </div>
                            </div>
                        @endif
                        @if ($couponDiscountAmount > 0)
                            <div class="row price discount-amount">
                                <div class="col-6">
                                    <p>{{ __('Coupon code discount amount') }}:</p>
                                </div>
                                <div class="col-6">
                                    <p class="price-text total-discount-amount-text"> {{ format_price($couponDiscountAmount) }} </p>
                                </div>
                            </div>
                        @endif
                        @if ($promotionDiscountAmount > 0)
                            <div class="row">
                                <div class="col-6">
                                    <p>{{ __('Promotion discount amount') }}:</p>
                                </div>
                                <div class="col-6">
                                    <p class="price-text"> {{ format_price($promotionDiscountAmount) }} </p>
                                </div>
                            </div>
                        @endif
                        @if (!empty($shipping))
                            <div class="row">
                                <div class="col-6">
                                    <p>{{ __('Shipping fee') }}:</p>
                                </div>
                                <div class="col-6 float-right">
                                    <p class="price-text shipping-price-text">{{ format_price($shippingAmount) }}</p>
                                </div>
                            </div>
                        @endif

                        @if (EcommerceHelper::isTaxEnabled())
                            <div class="row">
                                <div class="col-6">
                                    <p>{{ __('Tax') }}:</p>
                                </div>
                                <div class="col-6 float-right">
                                    <p class="price-text tax-price-text">{{ format_price(Cart::instance('cart')->rawTax()) }}</p>
                                </div>
                            </div>
                        @endif
                        <hr/>
                        <div class="row">
                            <div class="col-6">
                                <p>{{ __('Total') }}:</p>
                            </div>
                            <div class="col-6 float-right">
                                <p class="total-text raw-total-text"
                                   data-price="{{ Cart::instance('cart')->rawTotal() }}"> {{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount) }} </p>
                            </div>
                        </div>

                    </div>

                    <div>
                        <hr />
                        @include('plugins/ecommerce::themes.discounts.partials.form')
                        <hr />
                    </div>
                </div> <!-- /mobile display -->

                <div class="form-checkout">
                    <form action="{{ route('payments.checkout') }}" method="post">
                        @csrf

                        <div>
                            <h5 class="checkout-payment-title">{{ __('Shipping information') }}</h5>
                            <input type="hidden" value="{{ route('public.checkout.save-information', $token) }}" id="save-shipping-information-url">
                            @include('plugins/ecommerce::orders.partials.address-form', compact('sessionCheckoutData'))
                        </div>
                        <br>

                        <div id="shipping-method-wrapper">
                            <h5 class="checkout-payment-title">{{ __('Shipping method') }}</h5>
                            <div class="shipping-info-loading" style="display: none;">
                                <div class="shipping-info-loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            @if (!empty($shipping))
                                <div class="payment-checkout-form">
                                    <input type="hidden" name="shipping_option" value="{{ old('shipping_option', $defaultShippingOption) }}">
                                    <ul class="list-group list_payment_method">
                                        @foreach ($shipping as $shippingKey => $shippingItem)
                                            @foreach($shippingItem as $subShippingKey => $subShippingItem)
                                                @include('plugins/ecommerce::orders.partials.shipping-option', [
                                                     'defaultShippingMethod' => $defaultShippingMethod,
                                                     'defaultShippingOption' => $defaultShippingOption,
                                                     'shippingOption'        => $subShippingKey,
                                                     'shippingItem'          => $subShippingItem,
                                                ])
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p>{{ __('No shipping methods available!') }}</p>
                            @endif
                        </div>
                        <br>

                        <div>
                            <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                            <input type="hidden" name="amount" value="{{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount }}">
                            <input type="hidden" name="currency" value="{{ strtoupper(get_application_currency()->title) }}">
                            <input type="hidden" name="currency_id" value="{{ get_application_currency_id() }}">
                            <input type="hidden" name="callback_url" value="{{ route('public.payment.paypal.status') }}">
                            <input type="hidden" name="return_url" value="{{ route('public.checkout.success', $token) }}">
                            <ul class="list-group list_payment_method">
                                @if (setting('payment_stripe_status') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_stripe"
                                               value="stripe" @if (!setting('default_payment_method') || setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::STRIPE) checked @endif data-toggle="collapse" data-target=".payment_stripe_wrap" data-parent=".list_payment_method">
                                        <label for="payment_stripe" class="text-left">
                                            {{ setting('payment_stripe_name', trans('plugins/payment::payment.payment_via_card')) }}
                                        </label>
                                        <div class="payment_stripe_wrap payment_collapse_wrap collapse @if (!setting('default_payment_method') || setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::STRIPE) show @endif">
                                            <div class="card-checkout">
                                                <div class="form-group">
                                                    <div class="stripe-card-wrapper"></div>
                                                </div>
                                                <div class="form-group @if ($errors->has('number') || $errors->has('expiry')) has-error @endif">
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <input placeholder="{{ trans('plugins/payment::payment.card_number') }}"
                                                                   class="form-control" type="text" id="stripe-number" data-stripe="number">
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input placeholder="{{ trans('plugins/payment::payment.mm_yy') }}" class="form-control"
                                                                   type="text" id="stripe-exp" data-stripe="exp">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group @if ($errors->has('name') || $errors->has('cvc')) has-error @endif">
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <input placeholder="{{ trans('plugins/payment::payment.full_name') }}"
                                                                   class="form-control" id="stripe-name" type="text" data-stripe="name">
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input placeholder="{{ trans('plugins/payment::payment.cvc') }}" class="form-control"
                                                                   type="text" id="stripe-cvc" data-stripe="cvc">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="payment-stripe-key" data-value="{{ setting('payment_stripe_client_id') }}"></div>
                                        </div>
                                    </li>
                                @endif
                                @if (setting('payment_paypal_status') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_paypal"
                                               @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::PAYPAL) checked @endif
                                               value="paypal">
                                        <label for="payment_paypal" class="text-left">{{ setting('payment_paypal_name', trans('plugins/payment::payment.payment_via_paypal')) }}</label>
                                    </li>
                                @endif

                                {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, ['amount' => ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? 0 : Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount, 'currency' => strtoupper(get_application_currency()->title), 'name' => null]) !!}

                                @if (setting('payment_cod_status') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_cod"
                                               @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::COD) checked @endif
                                               value="cod" data-toggle="collapse" data-target=".payment_cod_wrap" data-parent=".list_payment_method">
                                        <label for="payment_cod" class="text-left">{{ setting('payment_cod_name', trans('plugins/payment::payment.payment_via_cod')) }}</label>
                                        <div class="payment_cod_wrap payment_collapse_wrap collapse @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::COD) show @endif" style="padding: 15px 0;">
                                            {!! clean(setting('payment_cod_description')) !!}
                                        </div>
                                    </li>
                                @endif
                                @if (setting('payment_bank_transfer_status') == 1)
                                    <li class="list-group-item">
                                        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_bank_transfer"
                                               @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) checked @endif
                                               value="bank_transfer" data-toggle="collapse" data-target=".payment_bank_transfer_wrap" data-parent=".list_payment_method">
                                        <label for="payment_bank_transfer" class="text-left">{{ setting('payment_bank_transfer_name', trans('plugins/payment::payment.payment_via_bank_transfer')) }}</label>
                                        <div class="payment_bank_transfer_wrap payment_collapse_wrap collapse @if (setting('default_payment_method') == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) show @endif" style="padding: 15px 0;">
                                            {!! clean(setting('payment_bank_transfer_description')) !!}
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <br>

                        <div class="form-group @if ($errors->has('description')) has-error @endif">
                            <label for="description" class="control-label">{{ __('Note') }}</label>
                            <br>
                            <textarea name="description" id="description" rows="3" class="form-control" placeholder="{{ __('Note') }}...">{{ old('description') }}</textarea>
                            {!! Form::error('description', $errors) !!}
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 d-none d-md-block" style="line-height: 53px">
                                    <a class="text-info" href="{{ route('public.cart') }}"><i class="fas fa-long-arrow-alt-left"></i> {{ __('Back to cart') }}</a>
                                </div>
                                <div class="col-md-6" style="margin-bottom: 40px">
                                    <button type="submit" class="btn payment-checkout-btn payment-checkout-btn-step float-right" data-processing-text="{{ __('Processing. Please wait...') }}" data-error-header="{{ __('Error') }}">
                                        {{ __('Checkout') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div> <!-- /form checkout -->

            </div>
            <!---------------------- start right column ---------------- -->
            <div class="col-lg-5 col-md-6 d-none d-md-block right"  id="main-checkout-product-info">
                <div class="payment-info-loading" style="display: none;">
                    <div class="payment-info-loading-content">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
                @if (isset($products) && $products)
                    @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                        @php
                            $product = $products->where('id', $cartItem->id)->first();
                        @endphp
                        @if(!empty($product))
                            <div class="row product-item">
                                <div class="col-lg-2 col-md-2">
                                    <div class="checkout-product-img-wrapper">
                                        <img class="item-thumb img-thumbnail img-rounded" src="{{ $cartItem->options['image']}}" alt="{{ $product->name ?? '' }}">
                                        <span class="checkout-quantity">{{ $cartItem->qty }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-5">
                                    <p style="margin-bottom: 0;">{{ $product->name }}</p>
                                    <p style="margin-bottom: 0">
                                        <small>
                                            @php
                                                $attributes = get_product_attributes($product->id);
                                            @endphp

                                            @if (!empty($attributes))
                                                @foreach ($attributes as $attr)
                                                    @if (!$loop->last)
                                                        {{ $attr->attribute_set_title }}: {{ $attr->title }},
                                                    @else
                                                        {{ $attr->attribute_set_title }}: {{ $attr->title }}
                                                    @endif
                                                @endforeach
                                            @endif
                                        </small>
                                    </p>
                                    @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                        @foreach($cartItem->options['extras'] as $option)
                                            @if (!empty($option['key']) && !empty($option['value']))
                                                <p style="margin-bottom: 0;"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-4 float-right">
                                    <p class="price-text">
                                        <span>{{ format_price($cartItem->price) }}</span>
                                    </p>
                                </div>
                            </div> <!--  /item -->
                        @endif
                    @endforeach
                @endif
                <hr />
                @include('plugins/ecommerce::themes.discounts.partials.form')
                <hr/>
                <div class="row price">
                    <div class="col-lg-7 col-md-8 col-5">
                        <p>{{ __('Subtotal') }}:</p>
                    </div>
                    <div class="col-lg-5 col-md-4 col-5">
                        <p class="price-text sub-total-text"> {{ format_price(Cart::instance('cart')->rawSubTotal()) }} </p>
                    </div>
                </div>
                @if (session('applied_coupon_code'))
                    <div class="row coupon-information">
                        <div class="col-lg-7 col-md-8 col-5">
                            <p>{{ __('Coupon code') }}:</p>
                        </div>
                        <div class="col-lg-5 col-md-4 col-5">
                            <p class="price-text coupon-code-text"> {{ session('applied_coupon_code') }} </p>
                        </div>
                    </div>
                @endif
                @if ($couponDiscountAmount > 0)
                    <div class="row price discount-amount">
                        <div class="col-lg-7 col-md-8 col-5">
                            <p>{{ __('Coupon code discount amount') }}:</p>
                        </div>
                        <div class="col-lg-5 col-md-4 col-5">
                            <p class="price-text total-discount-amount-text"> {{ format_price($couponDiscountAmount) }} </p>
                        </div>
                    </div>
                @endif
                @if ($promotionDiscountAmount > 0)
                    <div class="row">
                        <div class="col-lg-7 col-md-8 col-5">
                            <p>{{ __('Promotion discount amount') }}:</p>
                        </div>
                        <div class="col-lg-5 col-md-4 col-5">
                            <p class="price-text"> {{ format_price($promotionDiscountAmount) }} </p>
                        </div>
                    </div>
                @endif
                @if (!empty($shipping))
                    <div class="row shipment">
                        <div class="col-lg-7 col-md-8 col-5">
                            <p>{{ __('Shipping fee') }}:</p>
                        </div>
                        <div class="col-lg-5 col-md-4 col-5 float-right">
                            <p class="price-text shipping-price-text"> {{ format_price($shippingAmount) }} </p>
                        </div>
                    </div>
                @endif
                @if (EcommerceHelper::isTaxEnabled())
                    <div class="row shipment">
                        <div class="col-lg-7 col-md-8 col-5">
                            <p>{{ __('Tax') }}:</p>
                        </div>
                        <div class="col-lg-5 col-md-4 col-5 float-right">
                            <p class="price-text tax-price-text"> {{ format_price(Cart::instance('cart')->rawTax()) }} </p>
                        </div>
                    </div>
                @endif
                <hr/>
                <div class="row total-price">
                    <div class="col-lg-7 col-md-8 col-5">
                        <p>{{ __('Total') }}:</p>
                    </div>
                    <div class="col-lg-5 col-md-4 col-5 float-right">
                        <p class="total-text raw-total-text"
                           data-price="{{ Cart::instance('cart')->rawTotal() }}"> {{ ($promotionDiscountAmount + $couponDiscountAmount - $shippingAmount) > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount) }} </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if (theme_option('logo'))
                        <div class="checkout-logo">
                            <a href="{{ url('/') }}" title="{{ theme_option('site_title') }}">
                                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="img-fluid" width="150" alt="{{ theme_option('site_title') }}" />
                            </a>
                        </div>
                        <hr/>
                    @endif
                    <div class="alert alert-warning" style="margin: 50px auto;">
                        <span>{!! __('No products in cart. :link!', ['link' => Html::link(url('/'), __('Back to shopping'))]) !!}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="{{ asset('vendor/core/plugins/payment/libraries/card/card.js') }}"></script>
    @if (setting('payment_stripe_status') == 1)
        <script src="{{ asset('https://js.stripe.com/v2/') }}"></script>
    @endif
    <script src="{{ asset('vendor/core/plugins/payment/js/payment.js') }}?v=1.0.2"></script>

@stop
