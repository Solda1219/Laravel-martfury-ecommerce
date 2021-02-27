<div class="ps-my-account">
    <div class="container">
        <form class="ps-form--account ps-tab-root" method="GET" action="{{ route('public.orders.tracking') }}">
            <div class="ps-form__content">
                <h4 style="margin-bottom: 0;">{{ __('Order tracking') }}</h4>
                <p class="text-center" style="margin-bottom: 30px;">{{ __('Tracking your order status') }}</p>

                <div class="form-group">
                    <label for="txt-order-id">{{ __('Order ID') }}<sup>*</sup></label>
                    <input class="form-control" name="order_id" id="txt-order-id" type="text" value="{{ old('order_id', request()->input('order_id')) }}" placeholder="{{ __('Order ID') }}">
                    @if ($errors->has('order_id'))
                        <span class="text-danger">{{ $errors->first('order_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="txt-email">{{ __('Email Address') }}<sup>*</sup></label>
                    <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email', request()->input('email')) }}" placeholder="{{ __('Your Email') }}">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" type="submit">{{ __('Find') }}</button>
                </div>
            </div>
        </form>

        @if ($order)
            <div class="customer-order-detail">
                <div class="row">
                    <div class="col-md-6">
                        <h5>{{ __('Order information') }}</h5>
                        <p>
                            <span>{{ __('Order number') }}:</span>
                            <strong>{{ get_order_code($order->id) }}</strong>
                        </p>
                        <p>
                            <span>{{ __('Time') }}:</span> <strong>{{ $order->created_at->format('h:m d/m/Y') }}</strong>
                        </p>
                        <p>
                            <span>{{ __('Order status') }}:</span> <strong>{{ $order->status->label() }}</strong>
                        </p>

                        <p>
                            <span>{{ __('Payment method') }}:</span> <strong> {{ $order->payment->payment_channel->label() }} </strong>
                        </p>

                        <p>
                            <span>{{ __('Payment status') }}:</span> <strong>{{ $order->payment->status->label() }}</strong>
                        </p>

                    </div>
                    <div class="col-md-6 customer-information-box">
                        <h5>{{ __('Customer information') }}</h5>

                        <p>
                            <span>{{ __('Full Name') }}:</span> <strong>{{ $order->address->name }} </strong>
                        </p>

                        <p>
                            <span>{{ __('Phone') }}:</span> <strong>{{ $order->address->phone }} </strong>
                        </p>

                        <p>
                            <span>{{ __('Address') }}:</span> <strong> {{ $order->address->address }} </strong>
                        </p>

                        <p>
                            <span>{{ __('City') }}:</span> <strong>{{ $order->address->city }} </strong>
                        </p>
                        <p>
                            <span>{{ __('State') }}:</span> <strong> {{ $order->address->state }} </strong>
                        </p>
                        <p>
                            <span>{{ __('Country') }}:</span> <strong> {{ $order->address->country_name }} </strong>
                        </p>
                        @if (EcommerceHelper::isZipCodeEnabled())
                            <p>
                                <span>{{ __('Zip code') }}:</span> <strong> {{ $order->address->zip_code }} </strong>
                            </p>
                        @endif
                    </div>
                </div>
                <br>
                <h5>{{ __('Order detail') }}</h5>
                <div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">{{ __('Image') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th class="text-center">{{ __('Amount') }}</th>
                                <th class="text-right" style="width: 100px">{{ __('Quantity') }}</th>
                                <th class="price text-right">{{ __('Total') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->products as $key => $orderProduct)
                                @php
                                    $product = get_products([
                                        'condition' => [
                                            'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                                            'ec_products.id' => $orderProduct->product_id,
                                        ],
                                        'take' => 1,
                                        'select' => [
                                            'ec_products.id',
                                            'ec_products.images',
                                            'ec_products.name',
                                            'ec_products.price',
                                            'ec_products.sale_price',
                                            'ec_products.sale_type',
                                            'ec_products.start_date',
                                            'ec_products.end_date',
                                            'ec_products.sku',
                                            'ec_products.is_variation',
                                        ],
                                    ]);

                                @endphp
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">
                                        <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" width="50" alt="{{ $product->name }}"></td>
                                    <td>
                                        {{ $product->name }} @if ($product->sku) ({{ $product->sku }}) @endif
                                        @if ($product->is_variation)
                                            <p class="mb-0">
                                                <small>
                                                    @php $attributes = get_product_attributes($product->id) @endphp
                                                    @if (!empty($attributes))
                                                        @foreach ($attributes as $attribute)
                                                            {{ $attribute->attribute_set_title }}: {{ $attribute->title }}@if (!$loop->last), @endif
                                                        @endforeach
                                                    @endif
                                                </small>
                                            </p>
                                        @endif

                                        @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                            @foreach($orderProduct->options as $option)
                                                @if (!empty($option['key']) && !empty($option['value']))
                                                    <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ format_price($orderProduct->price, $orderProduct->currency) }}</td>
                                    <td class="text-center">{{ $orderProduct->qty }}</td>
                                    <td class="money text-right">
                                        <strong>
                                            {{ format_price($orderProduct->price * $orderProduct->qty, $orderProduct->currency) }}
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p>
                        <span>{{ __('Shipping fee') }}:</span> <strong>  {{ format_price($order->shipping_amount, $order->currency_id) }} </strong>
                    </p>

                    @if (EcommerceHelper::isTaxEnabled())
                        <p>
                            <span>{{ __('Tax') }}:</span> <strong> {{ format_price($order->tax_amount, $order->currency_id) }} </strong>
                        </p>
                    @endif

                    <p>
                        <span>{{ __('Discount') }}: </span> <strong> {{ format_price($order->discount_amount) }}</strong>
                        @if ($order->discount_amount)
                            @if ($order->coupon_code)
                                ({!! __('Coupon code: ":code"', ['code' => Html::tag('strong', $order->coupon_code)->toHtml()]) !!})
                            @elseif ($order->discount_description)
                                ({{ $order->discount_description }})
                            @endif
                        @endif
                    </p>

                    <p>
                        <span>{{ __('Total Amount') }}:</span> <strong> {{ format_price($order->amount, $order->currency_id) }} </strong>
                    </p>
                </div>
                @elseif (request()->input('order_id') || request()->input('email'))
                    <p class="text-center text-danger mt-40">{{ __('Order not found!') }}</p>
                @endif
            </div>
    </div>
</div>
