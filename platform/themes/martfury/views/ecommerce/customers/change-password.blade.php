@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    {!! Form::open(['route' => 'customer.post.change-password', 'class' => 'ps-form--account-setting', 'method' => 'POST']) !!}
    <div class="ps-form__header">
        <h3>{{ SeoHelper::getTitle() }}</h3>
    </div>
    <div class="ps-form__content">
        <div class="form-group @if ($errors->has('old_password')) has-error @endif">
            <label for="old_password">{{ __('Current password') }}:</label>
            <input type="password" class="form-control" name="old_password" id="old_password"
                   placeholder="{{ __('Current Password') }}">
            {!! Form::error('old_password', $errors) !!}
        </div>
        <div class="form-group @if ($errors->has('password')) has-error @endif">
            <label for="password">{{ __('New password') }}:</label>
            <input type="password" class="form-control" name="password" id="password"
                   placeholder="{{ __('New Password') }}">
            {!! Form::error('password', $errors) !!}
        </div>
        <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
            <label for="password_confirmation">{{ __('Password confirmation') }}:</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                   placeholder="{{ __('Password Confirmation') }}">
            {!! Form::error('password_confirmation', $errors) !!}
        </div>

        <div class="form-group text-center">
            <div class="form-group submit">
                <button class="ps-btn">{{ __('Update') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
