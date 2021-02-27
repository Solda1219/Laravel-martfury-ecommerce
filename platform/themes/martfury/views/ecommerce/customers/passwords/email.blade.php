<div class="ps-my-account">
    <div class="container">
        <form class="ps-form--account ps-tab-root" method="POST" action="{{ route('customer.password.request') }}">
            @csrf
            <div class="ps-form__content">
                <h4>{{ __('Reset Password') }}</h4>
                <div class="form-group">
                    <input class="form-control" type="email" value="{{ old('email') }}" placeholder="{{ __('Your Email') }}">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" type="submit">{{ __('Send Password Reset Link') }}</button>
                </div>

                @if (session('status'))
                    <div class="text-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success_msg'))
                    <div class="text-success">
                        {{ session('success_msg') }}
                    </div>
                @endif

                @if (session('error_msg'))
                    <div class="text-danger">
                        {{ session('error_msg') }}
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

