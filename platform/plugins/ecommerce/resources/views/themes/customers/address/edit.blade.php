@extends('plugins/ecommerce::themes.customers.master')

@section('content')
   <h2 class="customer-page-title">{{ __('Address books') }}</h2>
    <br>
    <div class="profile-content">

        {!! Form::open(['route' => ['customer.address.edit', $address->id]]) !!}
        <div class="input-group">
            <span class="input-group-prepend">{{ __('Full Name') }}:</span>
            <input id="name" type="text" class="form-control" name="name" value="{{ $address->name }}">
            {!! Form::error('name', $errors) !!}
        </div>

        <div class="input-group">
            <span class="input-group-prepend">{{ __('Email') }}:</span>
            <input id="email" type="text" class="form-control" name="email" value="{{ $address->email }}">
            {!! Form::error('email', $errors) !!}
        </div>

       <div class="input-group">
            <span class="input-group-prepend">{{ __('Phone') }}:</span>
            <input id="phone" type="text" class="form-control" name="phone" value="{{ $address->phone }}">
            {!! Form::error('phone', $errors) !!}
        </div>

        <div class="input-group @if ($errors->has('country')) has-error @endif">
            <span class="input-group-prepend">{{ __('Country') }}:</span>
            <select name="country" class="form-control" id="country">
                @foreach(['' => __('Select country...')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                    <option value="{{ $countryCode }}" @if ($address->country == $countryCode) selected @endif>{{ $countryName }}</option>
                @endforeach
            </select>
        </div>
        {!! Form::error('country', $errors) !!}

        <div class="input-group @if ($errors->has('state')) has-error @endif">
            <span class="input-group-prepend required ">{{ __('State') }}:</span>
            <input id="state" type="text" class="form-control" name="state" value="{{ $address->state }}">
            {!! Form::error('state', $errors) !!}
        </div>

        <div class="input-group @if ($errors->has('city')) has-error @endif">
            <span class="input-group-prepend required ">{{ __('City') }}:</span>
            <input id="city" type="text" class="form-control" name="city" value="{{ $address->city }}">
            {!! Form::error('city', $errors) !!}
        </div>

        <div class="input-group">
            <span class="input-group-prepend required ">{{ __('Address') }}:</span>
            <input id="address" type="text" class="form-control" name="address" value="{{ $address->address }}">
            {!! Form::error('address', $errors) !!}
        </div>

        @if (EcommerceHelper::isZipCodeEnabled())
            <div class="form-group">
                <label>{{ __('Zip code') }}:</label>
                <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{ $address->zip_code }}">
                {!! Form::error('zip_code', $errors) !!}
            </div>
        @endif

        <div class="form-group">
            <label for="is_default">
                <input class="customer-checkbox" type="checkbox" name="is_default" value="1" @if ($address->is_default) checked @endif id="is_default">
                {{ __('Use this address as default.') }}
                {!! Form::error('is_default', $errors) !!}
            </label>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
