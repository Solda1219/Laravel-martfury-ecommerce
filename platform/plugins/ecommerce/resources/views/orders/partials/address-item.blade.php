<div class="address-item @if ($address->is_default) is-default @endif" data-id="{{ $address->id }}">
    <p class="name">{{ $address->name }}</p>
    <p class="address"
       title="{{ $address->address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country_name }}@if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code), {{ $address->zip_code }} @endif">
        {{ $address->address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country_name }}@if (EcommerceHelper::isZipCodeEnabled() && $address->zip_code), {{ $address->zip_code }} @endif
    </p>
    <p class="address">{{ __('Phone') }}: {{ $address->phone }}</p>
    @if ($address->email)
        <p class="phone">{{ __('Email') }}: {{ $address->email }}</p>
    @endif
    @if ($address->is_default)
        <span class="default">{{ __('Default') }}</span>
    @endif
</div>
