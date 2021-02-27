<div class="ps-contact-form">
    <div class="container">
        <form action="{{ route('public.send.contact') }}" method="post" class="ps-form--contact-us contact-form">
            @csrf
            <h3>{{ __('Get In Touch') }}</h3>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
                    <div class="form-group">
                        <input class="form-control" name="name" type="text" placeholder="{{ __('Name *') }}">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="{{ __('Email *') }}">
                    </div>
                </div>
                <div class="col-12 ">
                    <div class="form-group">
                        <input class="form-control" type="text" name="phone" placeholder="{{ __('Phone') }}">
                    </div>
                </div>
                <div class="col-12 ">
                    <div class="form-group">
                        <textarea class="form-control" name="content" rows="6" minlength="10"
                                  placeholder="{{ __('Message') }} *" required></textarea>
                    </div>
                </div>
            </div>

            @if (setting('enable_captcha') && is_plugin_active('captcha'))
                <div class="form-group">
                    {!! Captcha::display() !!}
                </div>
            @endif

            <div class="form-group submit">
                <button class="ps-btn" type="submit">{{ __('Send message') }}</button>
            </div>

            <div class="contact-form-group">
                <div class="contact-message contact-success-message" style="display: none"></div>
                <div class="contact-message contact-error-message" style="display: none"></div>
            </div>
        </form>
    </div>
</div>
