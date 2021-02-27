<div class="ps-product-list mt-40 mb-40">
    <div class="ps-container">
        <div class="ps-section__header">
            <h3>{!! clean($title) !!}</h3>
            <ul class="ps-section__links">
                <li><a href="{{ route('public.products') }}">{{ __('View All') }}</a></li>
            </ul>
        </div>
        <featured-products-component url="{{ route('public.ajax.trending-products') }}"></featured-products-component>
    </div>
</div>
