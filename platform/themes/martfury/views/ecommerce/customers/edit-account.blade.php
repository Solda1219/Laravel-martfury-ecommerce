@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    {!! Form::open(['route' => 'customer.edit-account', 'class' => 'ps-form--account-setting', 'method' => 'POST']) !!}
        <div class="ps-form__header">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
        <div class="ps-form__content">
            <div class="form-group">
                <label for="name">{{ __('Full Name') }}:</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ auth('customer')->user()->name }}">
            </div>
            {!! Form::error('name', $errors) !!}

            <div class="form-group @if ($errors->has('dob')) has-error @endif">
                <label for="date_of_birth">{{ __('Date of birth') }}:</label>
                <input id="date_of_birth" type="text" class="form-control" name="dob" value="{{ auth('customer')->user()->dob }}">
            </div>
            {!! Form::error('dob', $errors) !!}
            <div class="form-group @if ($errors->has('email')) has-error @endif">
                <label for="email">{{ __('Email') }}:</label>
                <input id="email" type="text" class="form-control" disabled="disabled" value="{{ auth('customer')->user()->email }}" name="email">
            </div>
            {!! Form::error('email', $errors) !!}

            <div class="form-group @if ($errors->has('phone')) has-error @endif">
                <label for="phone">{{ __('Phone') }}</label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ __('Phone') }}" value="{{ auth('customer')->user()->phone }}">

            </div>
            {!! Form::error('phone', $errors) !!}

            <div class="form-group text-center">
                <div class="form-group submit">
                    <button class="ps-btn">{{ __('Update') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
