@extends('plugins/ecommerce::themes.customers.master')
@section('content')
    <h2 class="customer-page-title">{{ __('Order information') }}</h2>
    <div class="clearfix"></div>
    <br>

    <div class="customer-order-detail">

        <div class="row">
            <div class="col-md-6">
                <div class="order-slogan">
                    <img width="100" src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                         alt="{{ theme_option('site_title') }}">
                    <br/>
                    {{ setting('contact_address') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="order-meta">
                    <p><span>{{ __('Order number') }}:</span> <span
                            class="order-detail-value">{{ get_order_code($order->id) }}</span></p>
                    <span>{{ __('Time') }}:</span> <span
                        class="order-detail-value">{{ $order->created_at->format('h:m d/m/Y') }}</span>
                </div>
            </div>

        </div>

        <div class="row">

            <h5>{{ __('Order information') }}</h5>
            <div class="col-md-12">
                <span>{{ __('Order status') }}:</span> <span
                    class="order-detail-value">{{ $order->status->label() }}</span>
            </div>

            <div class="col-md-12">
                <span>{{ __('Payment method') }}:</span> <span
                    class="order-detail-value"> {{ $order->payment->payment_channel->label() }} </span>
                <br>
                <span>{{ __('Payment status') }}:</span> <span
                    class="order-detail-value">{{ $order->payment->status->label() }}</span>
            </div>

            <div class="col-md-12">
                <span>{{ __('Amount') }}:</span> <span
                    class="order-detail-value"> {{ format_price($order->amount, $order->currency_id) }} </span>
            </div>

            @if (EcommerceHelper::isTaxEnabled())
                <div class="col-md-12">
                    <span>{{ __('Tax') }}:</span> <span
                        class="order-detail-value"> {{ format_price($order->tax_amount, $order->currency_id) }} </span>
                </div>
            @endif

            <div class="col-md-12">
                <span>{{ __('Shipping fee') }}:</span> <span
                    class="order-detail-value">  {{ format_price($order->shipping_amount, $order->currency_id) }} </span>
            </div>

            <h5>{{ __('Customer information') }}</h5>

            <div class="col-md-12">
                <span>{{ __('Full Name') }}:</span> <span class="order-detail-value">{{ $order->address->name }} </span>
            </div>

            <div class="col-md-12">
                <span>{{ __('Phone') }}:</span> <span class="order-detail-value">{{ $order->address->phone }} </span>
            </div>

            <div class="col-md-12">
                <span>{{ __('Address') }}:</span> <span
                    class="order-detail-value"> {{ $order->address->address }} </span>
            </div>

            <div class="col-md-12">
                <span>{{ __('City') }}:</span> <span
                    class="order-detail-value">{{ $order->address->city }} </span>
                <span>{{ __('State') }}:</span> <span
                    class="order-detail-value"> {{ $order->address->state }} </span>
                <span>{{ __('Country') }}:</span> <span
                    class="order-detail-value"> {{ $order->address->country_name }} </span>
            </div>

            <h5>{{ __('Order detail') }}</h5>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('Image') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Quantity') }}</th>
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
                                    <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" width="50">
                                </td>
                                <td>
                                    {{ $product->name }} @if ($product->sku) ({{ $product->sku }}) @endif
                                    @if ($product->is_variation)
                                        <p>
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
                                <td>{{ format_price($orderProduct->price) }}</td>
                                <td class="text-center">{{ $orderProduct->qty }}</td>
                                <td class="money text-right">
                                    <strong>
                                        {{ format_price($orderProduct->price * $orderProduct->qty) }}
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-12">
                <a href="{{ route('customer.print-order', $order->id) }}" class="btn-print">{{ __('Print order') }}</a>
                @if ($order->canBeCanceled())
                    <a href="{{ route('customer.orders.cancel', $order->id) }}"
                       class="btn-print">{{ __('Cancel order') }}</a>
                @endif
            </div>
        </div>
@endsection
