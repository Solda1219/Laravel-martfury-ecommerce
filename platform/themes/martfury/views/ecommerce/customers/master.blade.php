 <section class="ps-section--account">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="ps-section__left">
                    <aside class="ps-widget--account-dashboard">
                        <div class="ps-widget__header">
                            <img src="{{ auth('customer')->user()->avatar_url }}" alt="{{ auth('customer')->user()->name }}">
                            <figure>
                                <figcaption>{{ __('Hello') }}</figcaption>
                                <p><a href="mailto:{{ auth('customer')->user()->email }}">{{ auth('customer')->user()->email }}</a></p>
                            </figure>
                        </div>
                        <div class="ps-widget__content">
                            <ul>
                                <li @if (Route::currentRouteName() == 'customer.overview') class="active" @endif><a href="{{ route('customer.overview') }}"><i class="icon-user"></i> {{ __('Account Information') }}</a></li>
                                <li @if (Route::currentRouteName() == 'customer.edit-account' || Route::currentRouteName() == 'customer.orders.view') class="active" @endif><a href="{{ route('customer.edit-account') }}"><i class="icon-pencil"></i> {{ __('Update profile') }}</a></li>
                                <li @if (Route::currentRouteName() == 'customer.orders') class="active" @endif><a href="{{ route('customer.orders') }}"><i class="icon-papers"></i> {{ __('Orders') }}</a></li>
                                <li @if (Route::currentRouteName() == 'customer.address' || Route::currentRouteName() == 'customer.address.create' || Route::currentRouteName() == 'customer.address.edit') class="active" @endif><a href="{{ route('customer.address') }}"><i class="icon-map-marker"></i> {{ __('Address') }}</a></li>
                                <li @if (Route::currentRouteName() == 'customer.change-password') class="active" @endif><a href="{{ route('customer.change-password') }}"><i class="icon-lock"></i> {{ __('Change password') }}</a></li>
                                <li><a href="{{ route('customer.logout') }}"><i class="icon-power-switch"></i>{{ __('Logout') }}</a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ps-section__right">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</section>
