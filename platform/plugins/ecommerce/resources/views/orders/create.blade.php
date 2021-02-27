@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order :currency="'{{ get_application_currency()->symbol }}'" :zip_code_enabled="{{ (int)EcommerceHelper::isZipCodeEnabled() }}"></create-order>
    </div>
@stop

@push('header')
    <script>
        "use strict";

        window.trans = {
            "Order": "{{ trans('plugins/ecommerce::order.order') }}",
            "Order information": "{{ trans('plugins/ecommerce::order.order_information') }}",
            "Create a new product": "{{ trans('plugins/ecommerce::order.create_new_product')  }}",
            "Out of stock": "{{ trans('plugins/ecommerce::order.out_of_stock') }}",
            "product(s) available": "{{ trans('plugins/ecommerce::order.products_available') }}",
            "No products found!": "{{ trans('plugins/ecommerce::order.no_products_found') }}",
        }
    </script>
@endpush
