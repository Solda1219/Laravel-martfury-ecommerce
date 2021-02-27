@extends('core/base::layouts.master')
@section('content')
    {!! Form::open(['url' => route('ecommerce.settings'), 'class' => 'main-setting-form']) !!}
        <div class="max-width-1200">
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::ecommerce.setting.title') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::store-locator.description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <label class="text-title-field" for="store_name">{{ trans('plugins/ecommerce::store-locator.shop_name') }}</label>
                            <input type="text" class="next-input" name="store_name" id="store_name" value="{{ get_ecommerce_setting('store_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="store_phone">{{ trans('plugins/ecommerce::store-locator.phone') }}</label>
                            <input type="text" class="next-input" name="store_phone" id="store_phone" value="{{ get_ecommerce_setting('store_phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="store_address">{{ trans('plugins/ecommerce::store-locator.address') }}</label>
                            <input type="text" class="next-input" name="store_address" id="store_address" value="{{ get_ecommerce_setting('store_address') }}">
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_state">{{ trans('plugins/ecommerce::ecommerce.setting.state') }}</label>
                                <input type="text" class="next-input" name="store_state" id="store_state" value="{{ get_ecommerce_setting('store_state') }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_city">{{ trans('plugins/ecommerce::ecommerce.setting.city') }}</label>
                                <input type="text" class="next-input" name="store_city" id="store_city" value="{{ get_ecommerce_setting('store_city') }}">
                            </div>
                        </div>
                        <div class="form-group mb0">
                            <label class="text-title-field" for="store_country">{{ trans('plugins/ecommerce::ecommerce.setting.country') }}</label>
                            <div class="ui-select-wrapper">
                                <select name="store_country" class="ui-select" id="store_country">
                                    @foreach(['' => trans('plugins/ecommerce::ecommerce.setting.select_country')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                                        <option value="{{ $countryCode }}" @if (get_ecommerce_setting('store_country') == $countryCode) selected @endif>{{ $countryName }}</option>
                                    @endforeach
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::ecommerce.standard_and_format') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::ecommerce.standard_and_format_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <label class="next-label">{{ trans('plugins/ecommerce::ecommerce.change_order_format') }}</label>
                        <p class="type-subdued">{{ trans('plugins/ecommerce::ecommerce.change_order_format_description', ['number' => config('plugins.ecommerce.order.default_order_start_number')]) }}</p>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_order_prefix">{{ trans('plugins/ecommerce::ecommerce.start_with') }}</label>
                                <div class="next-input--stylized">
                                    <span class="next-input-add-on next-input__add-on--before">#</span>
                                    <input type="text" class="next-input next-input--invisible" name="store_order_prefix" id="store_order_prefix" value="{{ get_ecommerce_setting('store_order_prefix') }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_order_suffix">{{ trans('plugins/ecommerce::ecommerce.end_with') }}</label>
                                <input type="text" class="next-input" name="store_order_suffix" id="store_order_suffix" value="{{ get_ecommerce_setting('store_order_suffix') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <p class="setting-note mb0">{{ trans('plugins/ecommerce::ecommerce.order_will_be_shown') }} <span class="sample-order-code">#<span class="sample-order-code-prefix">{{ get_ecommerce_setting('store_order_prefix') ? get_ecommerce_setting('store_order_prefix') . '-' : '' }}</span>{{ config('plugins.ecommerce.order.default_order_start_number') }}<span class="sample-order-code-suffix">{{ get_ecommerce_setting('store_order_suffix') ? '-' . get_ecommerce_setting('store_order_suffix') : '' }}</span></span> </p>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_weight_unit">{{ trans('plugins/ecommerce::ecommerce.weight_unit') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="store_weight_unit" id="store_weight_unit">
                                        <option value="g" @if (get_ecommerce_setting('store_weight_unit', 'g') === 'g') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.weight_unit_gram') }}</option>
                                        <option value="kg" @if (get_ecommerce_setting('store_weight_unit', 'g') === 'kg') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.weight_unit_kilogram') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_width_height_unit">{{ trans('plugins/ecommerce::ecommerce.height_unit') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="store_width_height_unit" id="store_width_height_unit">
                                        <option value="cm" @if (get_ecommerce_setting('store_width_height_unit', 'cm') === 'cm') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.height_unit_cm') }}</option>
                                        <option value="m" @if (get_ecommerce_setting('store_width_height_unit', 'cm') === 'm') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.height_unit_m') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::currency.currencies') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::currency.setting_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label class="text-title-field" for="thousands_separator">{{ trans('plugins/ecommerce::ecommerce.setting.thousands_separator') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="thousands_separator" id="thousands_separator">
                                        <option value="," @if (get_ecommerce_setting('thousands_separator', ',') == ',') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_comma') }}</option>
                                        <option value="." @if (get_ecommerce_setting('thousands_separator', ',') == '.') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_period') }}</option>
                                        <option value=" " @if (get_ecommerce_setting('thousands_separator', ',') == ' ') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_space') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-title-field" for="decimal_separator">{{ trans('plugins/ecommerce::ecommerce.setting.decimal_separator') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="decimal_separator" id="decimal_separator">
                                        <option value="." @if (get_ecommerce_setting('decimal_separator', '.') == '.') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_period') }}</option>
                                        <option value="," @if (get_ecommerce_setting('decimal_separator', '.') == ',') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_comma') }}</option>
                                        <option value=" " @if (get_ecommerce_setting('decimal_separator', '.') == ' ') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.separator_space') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    <textarea name="currencies"
                              id="currencies"
                              class="hidden">{!! json_encode($currencies) !!}</textarea>
                        <textarea name="deleted_currencies"
                                  id="deleted_currencies"
                                  class="hidden"></textarea>
                        <div class="swatches-container">
                            <div class="header clearfix">
                                <div class="swatch-item">
                                    {{ trans('plugins/ecommerce::currency.name') }}
                                </div>
                                <div class="swatch-item">
                                    {{ trans('plugins/ecommerce::currency.symbol') }}
                                </div>
                                <div class="swatch-item swatch-decimals">
                                    {{ trans('plugins/ecommerce::currency.number_of_decimals') }}
                                </div>
                                <div class="swatch-item swatch-exchange-rate">
                                    {{ trans('plugins/ecommerce::currency.exchange_rate') }}
                                </div>
                                <div class="swatch-item swatch-is-prefix-symbol">
                                    {{ trans('plugins/ecommerce::currency.is_prefix_symbol') }}
                                </div>
                                <div class="swatch-is-default">
                                    {{ trans('plugins/ecommerce::currency.is_default') }}
                                </div>
                                <div class="remove-item">{{ trans('plugins/ecommerce::currency.remove') }}</div>
                            </div>
                            <ul class="swatches-list">

                            </ul>
                            <a href="#" class="js-add-new-attribute">
                                {{ trans('plugins/ecommerce::currency.new_currency') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section store-locator-wrap">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::ecommerce.setting.store_locator_title') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::ecommerce.setting.store_locator_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <table class="table table-striped table-bordered table-header-color">
                            <thead>
                                <tr>
                                    <th>{{ trans('core/base::tables.name') }}</th>
                                    <th>{{ trans('core/base::tables.email') }}</th>
                                    <th>{{ trans('plugins/ecommerce::ecommerce.setting.phone') }}</th>
                                    <th>{{ trans('plugins/ecommerce::ecommerce.setting.address') }}</th>
                                    <th>{{ trans('plugins/ecommerce::ecommerce.setting.is_primary') }}</th>
                                    <th style="width: 120px;" class="text-right">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($storeLocators as $storeLocator)
                                <tr>
                                    <td>
                                        {{ $storeLocator->name }}
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $storeLocator->email }}">{{ $storeLocator->email }}</a>
                                    </td>
                                    <td>
                                        {{ $storeLocator->phone }}
                                    </td>
                                    <td>
                                        <span>{{ $storeLocator->address }}</span>,
                                        <span>{{ $storeLocator->city }}</span>,
                                        <span>{{ $storeLocator->state }}</span>,
                                        <span>{{ $storeLocator->country_name }}</span>
                                    </td>
                                    <td>
                                        {{ $storeLocator->is_primary ? trans('core/base::base.yes') : trans('core/base::base.no') }}
                                    </td>
                                    <td class="text-right">
                                        @if (!$storeLocator->is_primary && $storeLocators->count() > 1)
                                            <button class="btn btn-danger btn-small btn-trigger-delete-store-locator" data-target="{{ route('ecommerce.store-locators.destroy', $storeLocator->id) }}" type="button">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-small btn-trigger-show-store-locator" data-type="update" data-load-form="{{ route('ecommerce.store-locators.form', $storeLocator->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <a href="#" class="btn btn-primary btn-trigger-show-store-locator" data-type="create" data-load-form="{{ route('ecommerce.store-locators.form') }}">
                            {{ trans('plugins/ecommerce::ecommerce.setting.add_new') }}
                        </a>
                        @if (count($storeLocators) > 0)
                            <p style="margin-top: 10px">{{ trans('plugins/ecommerce::ecommerce.setting.or') }} <a href="#" data-toggle="modal" data-target="#change-primary-store-locator-modal">{{ trans('plugins/ecommerce::ecommerce.setting.change_primary_store') }}</a></p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::ecommerce.setting.other_settings') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::ecommerce.setting.other_settings_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <label class="text-title-field"
                                   for="shopping_cart_enabled">{{ trans('plugins/ecommerce::ecommerce.setting.enable_cart') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="shopping_cart_enabled" class="hrv-radio"
                                       value="1"
                                       @if (EcommerceHelper::isCartEnabled()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="shopping_cart_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!EcommerceHelper::isCartEnabled()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="ecommerce_tax_enabled">{{ trans('plugins/ecommerce::ecommerce.setting.enable_tax') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="ecommerce_tax_enabled" class="hrv-radio"
                                       value="1"
                                       @if (EcommerceHelper::isTaxEnabled()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="ecommerce_tax_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!EcommerceHelper::isTaxEnabled()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="review_enabled">{{ trans('plugins/ecommerce::ecommerce.setting.enable_review') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="review_enabled" class="hrv-radio"
                                       value="1"
                                       @if (EcommerceHelper::isReviewEnabled()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="review_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!EcommerceHelper::isReviewEnabled()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="enable_quick_buy_button">{{ trans('plugins/ecommerce::ecommerce.setting.enable_quick_buy_button') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="enable_quick_buy_button" class="hrv-radio"
                                       value="1"
                                       @if (EcommerceHelper::isQuickBuyButtonEnabled()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="enable_quick_buy_button" class="hrv-radio"
                                       value="0"
                                       @if (!EcommerceHelper::isQuickBuyButtonEnabled()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="quick_buy_target_page">{{ trans('plugins/ecommerce::ecommerce.setting.quick_buy_target') }}
                            </label>
                            <div class="ui-select-wrapper">
                                <select name="quick_buy_target_page" class="ui-select form-control select-full" id="quick_buy_target_page">
                                    <option value="checkout" @if (EcommerceHelper::getQuickBuyButtonTarget() == 'checkout') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.checkout_page') }}</option>
                                    <option value="cart" @if (EcommerceHelper::getQuickBuyButtonTarget() == 'cart') selected @endif>{{ trans('plugins/ecommerce::ecommerce.setting.cart_page') }}</option>
                                </select>
                                <svg class="svg-next-icon svg-next-icon-size-16">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                </svg>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="zip_code_enabled">{{ trans('plugins/ecommerce::ecommerce.setting.zip_code_enabled') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="zip_code_enabled" class="hrv-radio"
                                       value="1"
                                       @if (EcommerceHelper::isZipCodeEnabled()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="zip_code_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!EcommerceHelper::isZipCodeEnabled()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">
                    &nbsp;
                </div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('plugins/ecommerce::currency.save_settings') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    {!! Form::modalAction('add-store-locator-modal', trans('plugins/ecommerce::ecommerce.setting.add_location'), 'info', view('plugins/ecommerce::settings.store-locator-item', ['locator' => null])->render(), 'add-store-locator-button', trans('plugins/ecommerce::ecommerce.setting.save_location'), 'modal-md') !!}
    {!! Form::modalAction('update-store-locator-modal', trans('plugins/ecommerce::ecommerce.setting.edit_location'), 'info', view('plugins/ecommerce::settings.store-locator-item', ['locator' => null])->render(), 'update-store-locator-button', trans('plugins/ecommerce::ecommerce.setting.save_location'), 'modal-md') !!}
    {!! Form::modalAction('delete-store-locator-modal', trans('plugins/ecommerce::ecommerce.setting.delete_location'), 'info', trans('plugins/ecommerce::ecommerce.setting.delete_location_confirmation'), 'delete-store-locator-button', trans('plugins/ecommerce::ecommerce.setting.accept')) !!}
    {!! Form::modalAction('change-primary-store-locator-modal', trans('plugins/ecommerce::ecommerce.setting.change_primary_location'), 'info', view('plugins/ecommerce::settings.store-locator-change-primary', compact('storeLocators'))->render(), 'change-primary-store-locator-button', trans('plugins/ecommerce::ecommerce.setting.accept'), 'modal-sm') !!}
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/ecommerce::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/ecommerce::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
