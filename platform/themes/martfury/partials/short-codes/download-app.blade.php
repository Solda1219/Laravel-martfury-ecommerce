<div class="ps-download-app">
    <div class="ps-container">
        <div class="ps-block--download-app">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                        <div class="ps-block__thumbnail">
                            <img src="{{ RvMedia::getImageUrl($screenshot) }}" alt="screenshot">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                        <div class="ps-block__content">
                            <h3>{!! clean($title) !!}</h3>
                            <p>{!! clean($description) !!}</p>
                            <form class="ps-form--download-app" action="{{ route('public.ajax.send-download-app-links') }}" method="post">
                                @csrf
                                <div class="form-group--nest">
                                    <input class="form-control" type="email" name="email" placeholder="{{ __('Email Address') }}">
                                    <button class="ps-btn" type="submit">{{ __('Subscribe') }}</button>
                                </div>
                            </form>
                            <p class="download-link">
                                <a href="{{ (string) $androidAppUrl }}"><img src="{{ Theme::asset()->url('img/google-play.png') }}" alt="{{ __('Google Play') }}"></a>
                                <a href="{{ (string) $iosAppUrl }}"><img src="{{ Theme::asset()->url('img/app-store.png') }}" alt="{{ __('App Store') }}"></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
