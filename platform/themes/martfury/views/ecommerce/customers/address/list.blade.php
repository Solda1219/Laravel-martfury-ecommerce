@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')

@section('content')
    <div class="ps-section__header">
        <h3></h3>
        <div class="float-left">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
        <div class="float-right">
            <a class="add-address ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.create') }}">
                <span>{{ __('Add a new address') }}</span>
            </a>
        </div>
    </div>
    <div class="ps-section__content">
        <div class="table-responsive">
            <table class="table ps-table--whishlist">
                <thead>
                <tr>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('Is default?') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @if (count($addresses) > 0)
                    @foreach($addresses as $address)
                        <tr>
                            <td style="white-space: inherit;">
                                <p>{{ $address->name }}, {{ $address->address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country_name }}@if (EcommerceHelper::isZipCodeEnabled()), {{ $address->zip_code }} @endif - {{ $address->phone }}</p>
                            </td>
                            <td style="width: 120px;">
                                @if ($address->is_default) {{ __('Yes') }} @else {{ __('No') }} @endif
                            </td>
                            <td style="width: 140px;">
                                <a class="ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.edit', $address->id) }}">{{ __('Edit') }}</a>
                                <a class="ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.destroy', $address->id) }}">{{ __('Remove') }}</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">{{ __('No address!') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 justify-content-center pagination_style1">
            {!! $addresses->links() !!}
        </div>
    </div>
@endsection
