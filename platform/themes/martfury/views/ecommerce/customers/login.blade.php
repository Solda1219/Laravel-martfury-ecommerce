<div class="ps-my-account">
    <div class="container">
        <form class="ps-form--account ps-tab-root" method="POST" action="{{ route('customer.login.post') }}">
            @csrf
            <div class="ps-form__content">
                <h4>{{ __('Log In Your Account') }}</h4>
                <div class="form-group">
                    <input class="form-control" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('Your Email') }}">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group form-forgot">
                    <input class="form-control" type="password" name="password" placeholder="{{ __('Password') }}"><a href="{{ route('customer.password.reset') }}">{{ __('Forgot?') }}</a>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="ps-checkbox">
                        <input class="form-control" type="checkbox" name="remember" id="remember-me">
                        <label for="remember-me">{{ __('Remember me') }}</label>
                    </div>
                </div>
                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" type="submit">{{ __('Login') }}</button>
                </div>

                <div class="form-group">
                    <p class="text-center">{{ __("Don't Have an Account?") }} <a href="{{ route('customer.register') }}">{{ __('Sign up now') }}</a></p>
                </div>
            </div>
            <div class="ps-form__footer">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div>
        </form>
    </div>
</div>
