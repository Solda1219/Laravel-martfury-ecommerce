@if (count($sliders) > 0)
    <div class="ps-home-banner ps-home-banner--1">
        <div class="ps-container">
            @if (AdsManager::locationHasAds('top-slider-image-1') || AdsManager::locationHasAds('top-slider-image-2'))
                <div class="ps-section__left">
                    <div class="ps-carousel--nav-inside owl-slider" data-owl-auto="true" data-owl-loop="true" data-owl-speed="5000" data-owl-gap="0" data-owl-nav="true" data-owl-dots="true" data-owl-item="1" data-owl-item-xs="1" data-owl-item-sm="1" data-owl-item-md="1" data-owl-item-lg="1" data-owl-duration="1000" data-owl-mousedrag="on" data-owl-animate-in="fadeIn" data-owl-animate-out="fadeOut">
                        @foreach($sliders as $slider)
                            <div class="ps-banner bg--cover" data-background="{{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}">
                                @if ($slider->link)
                                    <a class="ps-banner__overlay" href="{{ url($slider->link) }}"></a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="ps-section__right">
                    <div class="ps-collection">
                        {!! AdsManager::display('top-slider-image-1') !!}
                    </div>
                    <div class="ps-collection">
                        {!! AdsManager::display('top-slider-image-2') !!}
                    </div>
                </div>
            @else
                <div class="ps-carousel--nav-inside owl-slider" data-owl-auto="true" data-owl-loop="true" data-owl-speed="5000" data-owl-gap="0" data-owl-nav="true" data-owl-dots="true" data-owl-item="1" data-owl-item-xs="1" data-owl-item-sm="1" data-owl-item-md="1" data-owl-item-lg="1" data-owl-duration="1000" data-owl-mousedrag="on" data-owl-animate-in="fadeIn" data-owl-animate-out="fadeOut">
                    @foreach($sliders as $slider)
                        <div class="ps-banner bg--cover" data-background="{{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}">
                            @if ($slider->link)
                                <a class="ps-banner__overlay" href="{{ url($slider->link) }}"></a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
