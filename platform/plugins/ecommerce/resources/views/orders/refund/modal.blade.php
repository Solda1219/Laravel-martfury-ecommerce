{!! Form::open(['url' => route('orders.refund', $order->id)]) !!}

<div class="next-form-section">
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <div class="table-wrap p-none">
                <table class="table border-bottom mb0">
                    <thead>
                    <tr>
                        <th class="text-left">{{ trans('plugins/ecommerce::products.form.product') }}</th>
                        <th class="text-center">{{ trans('plugins/ecommerce::products.form.price') }}</th>
                        <th class="text-center">{{ trans('plugins/ecommerce::products.form.quantity') }}</th>
                        <th class="text-center">{{ trans('plugins/ecommerce::products.form.restock_quantity') }}</th>
                        <th class="text-center">{{ trans('plugins/ecommerce::products.form.remain') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($order->products as $orderProduct)
                        @php
                            $product = get_products([
                                'condition' => [
                                    'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                                    'ec_products.id'     => $orderProduct->product_id,
                                ],
                                'take'   => 1,
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
                        @if ($product)
                            <tr>
                                <td class="text-left width-300-px">
                                    <a class="text-underline wordwrap"
                                       href="{{ route('products.edit', $product->original_product->id) }}" target="_blank" title="{{ $orderProduct->product_name }}">{{ $orderProduct->product_name }}</a>
                                </td>
                                <td class="text-center">
                                    <span>{{ format_price($orderProduct->price) }}</span>
                                </td>
                                <td class="text-center">{{ $orderProduct->qty }}</td>
                                <td class="text-center">{{ $orderProduct->restock_quantity }}</td>
                                <td class="text-right">
                                    @if ($orderProduct->qty - $orderProduct->restock_quantity > 0)
                                    <input class="j-refund-quantity width-50-px pl5 m-auto next-input p-none-r" name="products[{{ $orderProduct->product_id }}]" type="number"
                                           min="0" value="{{ $orderProduct->qty - $orderProduct->restock_quantity }}" />
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-wrap p-none">
                <table class="refund-totals">
                    <tbody>
                    @if ($order->products->sum('qty') - $order->products->sum('restock_quantity') > 0)
                        <tr>
                            <td class="text-right" colspan="2">
                                <label class="next-label inline mt10">

                                    <input type="checkbox" class="hrv-checkbox" checked>
                                    <span>
                                        {{ trans('plugins/ecommerce::order.return') }} <span class="total-restock-items">{{ $order->products->sum('qty') - $order->products->sum('restock_quantity') }}</span> {{ trans('plugins/ecommerce::order.products') }}
                                    </span>
                                </label>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right">{{ trans('plugins/ecommerce::products.form.shipping_fee') }}</td>
                        <td class="text-right quantity">
                            <span>{{ format_price($order->shipping_amount) }}</span>
                        </td>
                    </tr>
                    @if ($order->payment->refunded_amount)
                        <tr>
                            <td class="text-right">{{ trans('plugins/ecommerce::order.total_refund_amount') }}:</td>
                            <td class="text-right quantity text-no-bold">
                                <span>{{ format_price($order->payment->refunded_amount) }}</span>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right">{{ trans('plugins/ecommerce::order.total_amount_can_be_refunded') }}:</td>
                        <td class="text-right quantity text-no-bold">
                            @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                <span>{{ format_price(0) }}</span>
                            @else
                                <span>{{ format_price($order->payment->amount - $order->payment->refunded_amount) }}</span>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @if ($order->payment->status !== \Botble\Payment\Enums\PaymentStatusEnum::PENDING && ($order->payment->amount - $order->payment->refunded_amount) > 0)
                <div class="table-wrapper p-none">
                    <table class="refund-payments">
                        <tbody>
                        <tr>
                            <td class="p-xs">
                                <label class="ws-nm mb0">
                                    <i class="fa-cash-small"></i>
                                    <span class="ml5 v-a-t">{{ $order->payment->payment_channel->label() }}</span>
                                </label>
                            </td>
                            <td class="p-xs width-150-px">
                                <div class="next-input--stylized">
                                    <input type="text" class="next-input next-input--invisible input-mask-number input-sync-item" data-target=".refund-amount-text" name="refund_amount" value="{{ $order->payment->amount - $order->payment->refunded_amount }}">
                                    <span class="next-input-add-on next-input__add-on--before">{{ get_application_currency()->symbol }}</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div class="next-form-devide-hr"></div>
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field" for="refund-note">{{ trans('plugins/ecommerce::order.refund_reason') }}</label>
            <div>
                <input type="text" class="next-input" name="refund_note" id="refund-note" value="{{ $order->payment->refund_note }}">
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
