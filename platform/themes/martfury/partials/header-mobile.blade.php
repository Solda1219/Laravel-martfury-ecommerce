<header class="header header--mobile" data-sticky="true">
    <div class="header__top">
        <div class="header__left">
            <p>{{ theme_option('welcome_message') }}</p>
        </div>
        @if (is_plugin_active('ecommerce'))
            <div class="header__right">
                <ul class="navigation__extra">
                    <li><a href="{{ route('public.orders.tracking') }}">{{ __('Track your order') }}</a></li>
                    @php $currencies = get_all_currencies(); @endphp
                    @if (count($currencies) > 1)
                        <li>
                            <div class="ps-dropdown"><a href="{{ route('public.change-currency', get_application_currency()->title) }}"><span>{{ get_application_currency()->title }}</span></a>
                                <ul class="ps-dropdown-menu">
                                    @foreach ($currencies as $currency)
                                        <li><a href="{{ route('public.change-currency', $currency->title) }}"><span>{{ $currency->title }}</span></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
    <div class="navigation--mobile">
        <div class="navigation__left"><a class="ps-logo" href="{{ url('/') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a></div>
        @if (is_plugin_active('ecommerce'))
            <div class="navigation__right">
                <div class="header__actions">
                    <div class="ps-cart--mini">
                        <a class="header__extra btn-shopping-cart" href="javascript:void(0)">
                            <i class="icon-bag2"></i><span><i>{{ Cart::instance('cart')->count() }}</i></span>
                        </a>
                        <div class="ps-cart--mobile">
                            {!! Theme::partial('cart') !!}
                        </div>
                    </div>
                    <div class="ps-block--user-header">
                        <div class="ps-block__left"><a href="{{ route('customer.overview') }}"><i class="icon-user"></i></a></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if (is_plugin_active('ecommerce'))
        <div class="ps-search--mobile">
            <form class="ps-form--search-mobile" action="{{ route('public.products') }}" method="get">
                <div class="form-group--nest">
                    <input class="form-control" name="q" value="{{ request()->query('q') }}" type="text" placeholder="{{ __('Search something...') }}">
                    <button type="submit"><i class="icon-magnifier"></i></button>
                </div>
            </form>
        </div>
    @endif
</header>
