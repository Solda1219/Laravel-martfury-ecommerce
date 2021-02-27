@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200">
        @if ($shipment->status == \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED)
            <div class="ui-layout__item mb20">
                <div class="ui-banner ui-banner--status-warning">
                    <div class="ui-banner__ribbon">
                        <svg class="svg-next-icon svg-next-icon-size-20">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#alert-circle"></use>
                        </svg>
                    </div>
                    <div class="ui-banner__content">
                        <h2 class="ui-banner__title">{{ trans('plugins/ecommerce::shipping.shipment_canceled') }}</h2>
                        <div class="ws-nm">
                            {{ trans('plugins/ecommerce::shipping.at') }} <i>{{ BaseHelper::formatDate($shipment->updated_at, 'H:i d/m/Y') }}</i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flexbox-grid no-pd-none">
            <div class="flexbox-content">
                <div class="panel panel-default">
                    <div class="wrapper-content">
                        <div class="clearfix">
                            <div class="table-wrapper p-none">
                                <table class="order-totals-summary">
                                    <tbody>
                                    @foreach ($shipment->order->products as $orderProduct)
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
                                        @if ($product)
                                            <tr class="border-bottom">
                                                <td class="order-border text-center p-small">
                                                    <i class="fa fa-truck"></i>
                                                </td>
                                                <td class="order-border p-small">
                                                    <div class="flexbox-grid-default pl5 p-r5">
                                                        <div class="flexbox-auto-50">
                                                            <div class="wrap-img"><img class="thumb-image thumb-image-cartorderlist" src="{{ RvMedia::getImageUrl($product->original_product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" /></div>
                                                        </div>
                                                        <div class="flexbox-content">
                                                            <a class="wordwrap hide-print" href="{{ route('products.edit', $product->original_product->id) }}" title="{{ $orderProduct->product_name }}">{{ $orderProduct->product_name }}</a>
                                                            <span>
                                                            @php $attributes = get_product_attributes($product->id) @endphp
                                                                @if (!empty($attributes))
                                                                    @foreach ($attributes as $attr)
                                                                        {{ $attr->title }}
                                                                        @if (!$loop->last)
                                                                            /
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                        </span>
                                                            <p>{{ trans('plugins/ecommerce::shipping.sku') }} : <span>{{ $product->sku }}</span></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="order-border text-right p-small p-sm-r">
                                                    <strong class="item-quantity">{{ $orderProduct->qty }}</strong>
                                                    <span class="item-multiplier mr5">×</span><b class="color-blue-line-through">{{ format_price($orderProduct->price) }}</b>
                                                </td>
                                                <td class="order-border text-right p-small p-sm-r border-none-r">
                                                    <span>{{ format_price($orderProduct->price * $orderProduct->qty) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>

                                </table>
                                <div class="flexbox-grid-default p-t15 p-b15 height-light bg-order">
                                    <div class="flexbox-content">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td colspan="3" class="text-right p-sm-r border-none">
                                                    {{ trans('plugins/ecommerce::shipping.cash_on_delivery') }}:
                                                </td>
                                                <td class="text-right p-sm-r border-none">
                                                    <span>{{ format_price($shipment->cod_amount) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right p-sm-r border-none">
                                                    {{ trans('plugins/ecommerce::shipping.shipping_fee') }}:
                                                </td>
                                                <td class="text-right p-sm-r border-none">
                                                    <span>{{ format_price($shipment->price) }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($shipment->status != \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED)
                    <br>
                    <div class="shipment-actions">
                        <div class="dropdown btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="mr5">{{ trans('plugins/ecommerce::shipping.update_shipping_status') }}</span>
                                <span class="caret"></span>
                            </button>
                            <div class="dropdown dropdown-menu dropdown-ps-left applist-style animate-scale-dropdown min-width-200-px" role="menu" aria-labelledby="dropdownfilter">
                                <div>
                                    <ul class="applist-menu">
                                        <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::PICKING }}" data-target="{{ route('ecommerce.shipments.update-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::PICKING()->label() }}</a></li>
                                        <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::DELIVERING }}" data-target="{{ route('ecommerce.shipments.update-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::DELIVERING()->label() }}</a></li>
                                        <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::DELIVERED }}" data-target="{{ route('ecommerce.shipments.update-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::DELIVERED()->label() }}</a></li>
                                        <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::NOT_DELIVERED }}" data-target="{{ route('ecommerce.shipments.update-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::NOT_DELIVERED()->label() }}</a></li>
                                        <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED }}" data-target="{{ route('ecommerce.shipments.update-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED()->label() }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if ($shipment->cod_amount)
                            <div class="dropdown btn-group p-l10">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="mr5">{{ trans('plugins/ecommerce::shipping.update_cod_status') }}</span>
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown dropdown-menu dropdown-ps-left applist-style animate-scale-dropdown min-width-200-px" role="menu" aria-labelledby="dropdownfilter">
                                    <div>
                                        <ul class="applist-menu">
                                            <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingCodStatusEnum::PENDING }}" data-target="{{ route('ecommerce.shipments.update-cod-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingCodStatusEnum::PENDING()->label() }}</a></li>
                                            <li><a data-value="{{ \Botble\Ecommerce\Enums\ShippingCodStatusEnum::COMPLETED }}" data-target="{{ route('ecommerce.shipments.update-cod-status', $shipment->id) }}">{{ \Botble\Ecommerce\Enums\ShippingCodStatusEnum::COMPLETED()->label() }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="mt20 mb20 timeline-shipment">
                    <div class="comment-log ws-nm">
                        <div class="comment-log-title">
                            <label class="bold-light m-xs-b hide-print">{{ trans('plugins/ecommerce::shipping.history') }}</label>
                        </div>
                        <div class="comment-log-timeline">
                            <div class="column-left-history ps-relative" id="order-history-wrapper">
                                @foreach ($shipment->histories()->latest()->get() as $history)
                                    <div class="item-card">
                                        <div class="item-card-body clearfix">
                                            <div class="item comment-log-item comment-log-item-date ui-feed__timeline">
                                                <div class="ui-feed__item ui-feed__item--message">
                                                    <span class="ui-feed__marker @if ($history->user_id) ui-feed__marker--user-action @endif"></span>
                                                    <div class="ui-feed__message">
                                                        <div class="timeline__message-container">
                                                            <div class="timeline__inner-message">
                                                                <span>{!! OrderHelper::processHistoryVariables($history) !!}</span>
                                                            </div>
                                                            <time class="timeline__time"><span>{{ $history->created_at }}</span></time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flexbox-content flexbox-right">
                <div class="wrapper-content">
                    <div class="pd-all-20">
                        <label class="title-product-main text-no-bold">{{ trans('plugins/ecommerce::shipping.shipment_information') }}</label>
                    </div>
                    <div class="pd-all-20 p-t15 p-b15 border-top-title-main ps-relative">
                        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding mb10">
                            <div class="flexbox-grid-form-item">
                                {{ trans('plugins/ecommerce::shipping.order_number') }}
                            </div>
                            <div class="flexbox-grid-form-item text-right">
                                <a target="_blank" href="{{ route('orders.edit', $shipment->order->id) }}" class="hover-underline">{{ get_order_code($shipment->order->id) }}</a>
                            </div>
                        </div>
                        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding mb10">
                            <div class="flexbox-grid-form-item">
                                {{ trans('plugins/ecommerce::shipping.shipping_method') }}
                            </div>
                            <div class="flexbox-grid-form-item text-right ws-nm">
                                <label class="font-size-11px">{{ OrderHelper::getShippingMethod($shipment->order->shipping_method) }}
                                    @if ($shipment->order->shipping_option)
                                        ({{ $shipment->order->shipping_method_name }})
                                    @endif
                                </label>
                            </div>
                        </div>
                        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding mb10">
                            <div class="flexbox-grid-form-item">
                                {{ trans('plugins/ecommerce::shipping.cod_status') }}
                            </div>
                            <div class="flexbox-grid-form-item text-right">
                                <label class="label codstatus_2">{{ $shipment->cod_status->label() }}</label>
                            </div>
                        </div>
                        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding mb10">
                            <div class="flexbox-grid-form-item">
                                {{ trans('plugins/ecommerce::shipping.shipping_status') }}
                            </div>
                            <div class="flexbox-grid-form-item text-right">
                                <label class="label carrierstatus_2">{{ $shipment->status->label() }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wrapper-content mt20">
                    <div class="pd-all-20">
                        <label class="title-product-main text-no-bold">{{ trans('plugins/ecommerce::shipping.customer_information') }}</label>
                    </div>
                    <div class="pd-all-20 p-t15 p-b15 border-top-title-main ps-relative">
                        <div class="form-group ws-nm mb0">
                            <ul class="ws-nm text-infor-subdued shipping-address-info">
                                @include('plugins/ecommerce::orders.shipping-address.detail', ['address' => $shipment->order->address])
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::modalAction('confirm-change-status-modal', trans('plugins/ecommerce::shipping.change_status_confirm_title'), 'info', trans('plugins/ecommerce::shipping.change_status_confirm_description'), 'confirm-change-shipment-status-button', trans('plugins/ecommerce::shipping.accept')) !!}
@stop
