{!! Form::open(['url' => route('orders.update-shipping-address', $address->id ?? 0)]) !!}
    <input type="hidden" name="order_id" value="{{ $orderId }}">
    <div class="next-form-section">
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.form_name') }}</label>
                <input type="text" class="next-input" name="name" placeholder="{{ trans('plugins/ecommerce::shipping.form_name') }}" value="{{ $address->name }}">
            </div>
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.phone') }}</label>
                <input type="text" class="next-input" name="phone" placeholder="{{ trans('plugins/ecommerce::shipping.phone') }}" value="{{ $address->phone }}">
            </div>
        </div>
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ trans('plugins/ecommerce::shipping.email') }}</label>
                <input type="text" class="next-input" name="email" placeholder="{{ trans('plugins/ecommerce::shipping.email') }}" value="{{ $address->email }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.country') }}</label>
                <select name="country" class="form-control" >
                    @foreach(['' => trans('plugins/ecommerce::shipping.select_country')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                        <option value="{{ $countryCode }}" @if ($address->country == $countryCode) selected @endif>{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.state') }}</label>
                <input type="text" class="next-input" name="state" placeholder="{{ trans('plugins/ecommerce::shipping.state') }}" value="{{ $address->state }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.city') }}</label>
                <input type="text" class="next-input" name="city" placeholder="{{ trans('plugins/ecommerce::shipping.city') }}" value="{{ $address->city }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.address') }}</label>
                <input type="text" class="next-input" name="address" placeholder="{{ trans('plugins/ecommerce::shipping.address') }}" value="{{ $address->address }}">
            </div>
        </div>

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="next-form-grid">
                <div class="next-form-grid-cell">
                    <label class="text-title-field required">{{ trans('plugins/ecommerce::shipping.zip_code') }}</label>
                    <input type="text" class="next-input" name="zip_code" placeholder="{{ trans('plugins/ecommerce::shipping.zip_code') }}" value="{{ $address->zip_code }}">
                </div>
            </div>
        @endif

    </div>
{!! Form::close() !!}
