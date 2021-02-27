@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    {!! Form::open(['route' => 'customer.address.create', 'class' => 'ps-form--account-setting', 'method' => 'POST']) !!}
        <div class="ps-form__header">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
        <div class="ps-form__content">
            <div class="form-group">
                <label for="name">{{ __('Full Name') }}:</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
            </div>
            {!! Form::error('name', $errors) !!}

            <div class="form-group">
                <label for="email">{{ __('Email') }}:</label>
                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}">
            </div>
            {!! Form::error('email', $errors) !!}

            <div class="form-group">
                <label for="phone">{{ __('Phone') }}:</label>
                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}">

            </div>
            {!! Form::error('phone', $errors) !!}

            <div class="form-group @if ($errors->has('country')) has-error @endif">
                <label for="country">{{ __('Country') }}:</label>
                <select name="country" class="form-control" id="country">
                    @foreach(['' => __('Select country...')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                        <option value="{{ $countryCode }}" @if (old('country') == $countryCode) selected @endif>{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>
            {!! Form::error('country', $errors) !!}

            <div class="form-group @if ($errors->has('state')) has-error @endif">
                <label for="state">{{ __('State') }}:</label>
                <input id="state" type="text" class="form-control" name="state" value="{{ old('state') }}">

            </div>
            {!! Form::error('state', $errors) !!}

            <div class="form-group @if ($errors->has('city')) has-error @endif">
                <label for="city">{{ __('City') }}:</label>
                <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}">

            </div>
            {!! Form::error('city', $errors) !!}

            <div class="form-group">
                <label for="address">{{ __('Address') }}:</label>
                <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}">
            </div>
            {!! Form::error('address', $errors) !!}

            @if (EcommerceHelper::isZipCodeEnabled())
                <div class="form-group">
                    <label>{{ __('Zip code') }}:</label>
                    <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{ old('zip_code') }}">
                    {!! Form::error('zip_code', $errors) !!}
                </div>
            @endif

            <div class="form-group">
                <div class="ps-checkbox">
                    <input class="form-control" type="checkbox" value="1" name="is_default" id="is-default">
                    <label for="is-default">{{ __('Use this address as default') }}</label>
                </div>
                {!! Form::error('is_default', $errors) !!}
            </div>

            <div class="form-group">
                <button class="ps-btn ps-btn--sm" type="submit">{{ __('Add a new address') }}</button>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
