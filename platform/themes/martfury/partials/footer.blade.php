 <footer class="ps-footer">
        <div class="ps-container">
            <div class="ps-footer__widgets">
                <aside class="widget widget_footer widget_contact-us">
                    <h4 class="widget-title">{{ __('Contact us') }}</h4>
                    <div class="widget_content">
                        <p>{{ __('Call us 24/7') }}</p>
                        <h3>{{ theme_option('hotline') }}</h3>
                        <!-- <p>{{ theme_option('address') }} <br><a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a></p> -->
                        <p>Address for Rafas</p>
                        <ul class="ps-list--social">
                            @for($i = 1; $i <= 5; $i++)
                                @if(theme_option('social-name-' . $i) && theme_option('social-url-' . $i) && theme_option('social-icon-' . $i))
                                    <li>
                                        <a href="{{ theme_option('social-url-' . $i) }}"
                                           title="{{ theme_option('social-name-' . $i) }}" style="color: {{ theme_option('social-color-' . $i) }}">
                                            <i class="fa {{ theme_option('social-icon-' . $i) }}"></i>
                                        </a>
                                    </li>
                                @endif
                            @endfor
                        </ul>
                    </div>
                </aside>
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
            <div class="ps-footer__links" id="footer-links">
                {!! dynamic_sidebar('bottom_footer_sidebar') !!}
            </div>
            <div class="ps-footer__copyright">
                <!-- <p>{{ theme_option('copyright') }}</p> -->
                <p>@ 2021 Rafas. All Rights Reserved.</p>
                <p>
                    <span>{{ __('We Using Safe Payment For') }}:</span>
                    @foreach(json_decode(theme_option('payment_methods', []), true) as $method)
                        @if (!empty($method))
                            <span><img src="{{ RvMedia::getImageUrl($method) }}" alt="payment method"></span>
                        @endif
                    @endforeach
                </p>
            </div>
        </div>
    </footer>

    @if (is_plugin_active('newsletter') && theme_option('enable_newsletter_popup', 'yes') === 'yes')
        <div data-session-domain="{{ config('session.domain') ?? request()->getHost() }}"></div>
        <div class="ps-popup" id="subscribe" data-time="500" style="display: none">
            <div class="ps-popup__content bg--cover" data-background="{{ RvMedia::getImageUrl(theme_option('newsletter_image')) }}"><a class="ps-popup__close" href="#"><i class="icon-cross"></i></a>
                <form method="post" action="{{ route('public.newsletter.subscribe') }}" class="ps-form--subscribe-popup newsletter-form">
                    @csrf
                    <div class="ps-form__content">
                        <h4>{{ __('Get 25% Discount') }}</h4>
                        <p>{{ __('Subscribe to the mailing list to receive updates on new arrivals, special offers and our promotions.') }}</p>
                        <div class="form-group">
                            <input class="form-control" name="email" type="email"  placeholder="{{ __('Email Address') }}" required>
                        </div>

                        @if (setting('enable_captcha') && is_plugin_active('captcha'))
                            <div class="form-group">
                                {!! Captcha::display() !!}
                            </div>
                        @endif

                        <div class="form-group">
                            <button class="ps-btn" type="submit" >{{ __('Subscribe') }}</button>
                        </div>
                        <div class="ps-checkbox">
                            <input class="form-control" type="checkbox" id="dont_show_again" name="dont_show_again">
                            <label for="dont_show_again">{{ __("Don't show this popup again") }}</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {!! Theme::get('bottomFooter') !!}

    <div id="back2top"><i class="icon icon-arrow-up"></i></div>
    <div class="ps-site-overlay"></div>
    @if (is_plugin_active('ecommerce'))
        <div class="ps-search" id="site-search"><a class="ps-btn--close" href="#"></a>
            <div class="ps-search__content">
                <form class="ps-form--primary-search" action="{{ route('public.products') }}" method="post">
                    <input class="form-control" name="q" value="{{ request()->query('q') }}" type="text" placeholder="{{ __('Search for...') }}">
                    <button><i class="aroma-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
    @endif
    <div class="modal fade" id="product-quickview" tabindex="-1" role="dialog" aria-labelledby="product-quickview" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content"><span class="modal-close" data-dismiss="modal"><i class="icon-cross2"></i></span>
                <article class="ps-product--detail ps-product--fullwidth ps-product--quickview">
                </article>
            </div>
        </div>
    </div>

    <script>
        window.trans = {
            "View All": "{{ __('View All') }}",
        }
        window.siteUrl = "{{ url('') }}";
    </script>

    {!! Theme::footer() !!}
    </body>
</html>
