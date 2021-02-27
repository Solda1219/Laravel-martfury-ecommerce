@extends('core/base::layouts.master')
@section('content')
    {!! Form::open() !!}
        <div id="main-discount">
            <div class="max-width-1200">
                <discount-component currency="{{ get_application_currency()->symbol }}"></discount-component>
            </div>
        </div>
    {!! Form::close() !!}
@stop

@push('header')
    <script>
        'use strict';

        window.trans = {
            "Discount": "{{ trans('plugins/ecommerce::discount.discount') }}",
        }

        $(document).ready(function () {
            $(document).on('click', 'body', function (e) {
                let container = $('.box-search-advance');

                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.find('.panel').addClass('hidden');
                }
            });
        });
    </script>
    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
