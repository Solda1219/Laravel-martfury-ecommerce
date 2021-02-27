@extends('core/base::layouts.master')

@section('content')

    <div class="row">
        <div class="col-lg-3  col-sm-6">
            @include('plugins/ecommerce::reports.partials.count-sell')
        </div>
        <div class="col-lg-3  col-sm-6">
            @include('plugins/ecommerce::reports.partials.count-orders')
        </div>
        <div class="col-lg-3  col-sm-6">
            @include('plugins/ecommerce::reports.partials.count-products')
        </div>
        <div class="col-lg-3  col-sm-6">
            @include('plugins/ecommerce::reports.partials.count-customers')
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12 widget_item" id="revenue-report" data-url="{{ route('ecommerce.report.revenue') }}">
            <div class="portlet light bordered portlet-no-padding">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject font-dark">{{ trans('plugins/ecommerce::reports.revenue_statistics')  }}</span>
                    </div>
                    @include('plugins/ecommerce::reports.tools')
                </div>
                <div class="row portlet-body widget-content" style="padding: 15px !important;">

                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12 widget_item" id="top-selling-products-report" data-url="{{ route('ecommerce.report.top-selling-products') }}">
            <div class="portlet light bordered portlet-no-padding">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject font-dark">{{ trans('plugins/ecommerce::reports.top_selling_products')  }}</span>
                    </div>
                    @include('plugins/ecommerce::reports.tools')
                </div>
                <div class="row portlet-body widget-content equal-height" style="padding: 15px 30px !important;">
                    {!! $topSellingProducts->renderTable() !!}
                </div>
            </div>
        </div>
    </div>
@stop
