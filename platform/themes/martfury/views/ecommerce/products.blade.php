<div class="ps-page--shop">
    <div class="ps-container" id="app">
        <div class="mt-40">
            <featured-brands-component url="{{ route('public.ajax.featured-brands') }}"></featured-brands-component>
        </div>
        <div class="ps-layout--shop">
            <div class="ps-layout__left">
                <form action="{{ route('public.products') }}" method="GET">
                    @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                </form>
            </div>
            <div class="ps-layout__right">
                <div class="ps-block--shop-features">
                    <div class="ps-block__header">
                        <h3>{{ __('Recommended Items') }}</h3>
                        <div class="ps-block__navigation">
                            <a class="ps-carousel__prev" href="#recommended-products">
                                <i class="icon-chevron-left"></i>
                            </a>
                            <a class="ps-carousel__next" href="#recommended-products">
                                <i class="icon-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ps-block__content">
                        <featured-products-component url="{{ route('public.ajax.featured-products') }}" :id="'recommended-products'"></featured-products-component>
                    </div>
                </div>
                <div class="ps-shopping ps-tab-root">
                    <div class="ps-shopping__header">
                        <p><strong> {{ $products->total() }}</strong> {{ __('Products found') }}</p>
                        <div class="ps-shopping__actions">
                            <form action="{{ URL::current() }}" method="GET">
                                @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/sort')
                            </form>
                            <div class="ps-shopping__view">
                                <ul class="ps-tab-list">
                                    <li class="active"><a href="#tab-1"><i class="icon-grid"></i></a></li>
                                    <li><a href="#tab-2"><i class="icon-list4"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <header class="header--mobile-categories d-sm-none d-block">
                        <div class="header__filter">
                            <button class="ps-shop__filter-mb" id="filter-sidebar">
                                <i class="icon-equalizer"></i><span>{{ __('Filter') }}</span>
                            </button>
                            <div class="header__sort">
                                <i class="icon-sort-amount-desc"></i>
                                <form action="{{ URL::current() }}" method="GET">
                                    @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/sort')
                                </form>
                            </div>
                        </div>
                    </header>
                    <div class="ps-tabs">
                        <div class="ps-tab active" id="tab-1">
                            <div class="ps-shopping-product">
                                <div class="row">
                                    @if ($products->count() > 0)
                                        @foreach($products as $product)
                                            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6 ">
                                                <div class="ps-product">
                                                    {!! Theme::partial('product-item', compact('product')) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="ps-pagination">
                                {!! $products->withQueryString()->links() !!}
                            </div>
                        </div>
                        <div class="ps-tab" id="tab-2">
                            <div class="ps-shopping-product">
                                @if ($products->count() > 0)
                                    @foreach($products as $product)
                                        <div class="ps-product ps-product--wide">
                                            {!! Theme::partial('product-item-grid', compact('product')) !!}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="ps-pagination">
                                {!! $products->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ps-filter--sidebar">
    <div class="ps-filter__header">
        <h3>{{ __('Filter Products') }}</h3><a class="ps-btn--close ps-btn--no-boder" href="#"></a>
    </div>
    <div class="ps-filter__content">
        <form action="{{ route('public.products') }}" method="GET">
            @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
        </form>
    </div>
</div>
