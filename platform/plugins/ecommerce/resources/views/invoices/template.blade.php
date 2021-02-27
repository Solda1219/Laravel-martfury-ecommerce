<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('plugins/ecommerce::order.invoice_for_order') }} {{ get_order_code($order->id) }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/ecommerce/css/invoice.css') }}">
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="5">
                    <table>
                        <tr>
                            <td style="width: 150px;">
                                @if(theme_option('logo'))
                                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" style="width:100%; max-width:150px;" alt="{{ theme_option('site_title') }}">
                                @endif
                            </td>

                            <td>
                                {{ trans('plugins/ecommerce::order.invoice') }}: {{ get_order_code($order->id) }}<br>
                                {{ trans('plugins/ecommerce::order.created') }}: {{ now()->format('F d, Y') }}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="5">
                    <table>
                        <tr>
                            <td>
                                {{ $order->address->name }}<br>
                                {{ $order->address->address }}<br>
                                {{ $order->address->email }} <br>
                                {{ $order->address->phone ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $order->user->name ? $order->user->name : $order->address->name }}<br>
                                {{ $order->user->email ? $order->user->email : $order->address->email }}<br>
                                {{ $order->user->phone ? $order->user->phone : $order->address->phone }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="4">
                    {{ trans('plugins/ecommerce::order.payment_method') }}
                </td>

                <td>
                    {{ trans('plugins/ecommerce::order.payment_status_label') }}
                </td>
            </tr>

            <tr class="details">
                <td colspan="4">
                    {{ $order->payment->payment_channel->label() }}
                </td>

                <td>
                    {{ $order->payment->status->label() }}
                </td>
            </tr>

            <tr class="heading">
                <th>{{ trans('plugins/ecommerce::products.form.product') }}</th>
                <th>{{ trans('plugins/ecommerce::products.form.options') }}</th>
                <th>{{ trans('plugins/ecommerce::products.form.price') }}</th>
                <th>{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
                <th>{{ trans('plugins/ecommerce::products.form.total') }}</th>
            </tr>

            @foreach ($order->products as $orderProduct)
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
                        ],
                    ]);
                @endphp
                @if(!empty($product))
                    <tr class="item">
                        <td> {{ $product->name }} </td>
                        <td>
                            @php $attributes = get_product_attributes($product->id); @endphp
                            @if (!empty($attributes))
                                @foreach ($attributes as $attribute)
                                    @if (!$loop->last)
                                        {{ $attribute->attribute_set_title }}: {{ $attribute->title }} <br>
                                    @else
                                        {{ $attribute->attribute_set_title }}: {{ $attribute->title }}
                                    @endif
                                @endforeach
                            @endif
                            @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                @foreach($orderProduct->options as $option)
                                    @if (!empty($option['key']) && !empty($option['value']))
                                        <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if ($product->front_sale_price != $product->price)
                                {{ format_price($product->front_sale_price) }}
                                <del>{{ format_price($product->price) }}</del>
                            @else
                                {{ format_price($product->price) }}
                            @endif
                        </td>
                        <td> {{ $orderProduct->qty }} </td>
                        <td>
                            @if ($product->front_sale_price != $product->price)
                                {{ format_price($product->front_sale_price * $orderProduct->qty) }}
                            @else
                                {{ format_price($product->price * $orderProduct->qty) }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach

            <tr class="total">
                <td colspan="4">&nbsp;</td>
                <td>
                    <p>{{ trans('plugins/ecommerce::products.form.sub_total') }}: {{ format_price($order->sub_total) }}</p>
                    <p>{{ trans('plugins/ecommerce::products.form.shipping_fee') }}: {{ format_price($order->shipping_amount) }}</p>
                    <p>{{ trans('plugins/ecommerce::products.form.discount') }}: {{ format_price($order->discount_amount) }}</p>
                    <p>{{ trans('plugins/ecommerce::products.form.total') }}: {{ format_price($order->amount) }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
