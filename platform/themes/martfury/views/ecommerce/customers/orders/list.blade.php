@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="ps-section__header">
        <h3>{{ SeoHelper::getTitle() }}</h3>
    </div>
    <div class="ps-section__content">
        <div class="table-responsive">
            <table class="table ps-table--whishlist">
                <thead>
                    <tr>
                        <th>{{ __('ID number') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @if (count($orders) > 0)
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ get_order_code($order->id) }}</td>
                            <td>{{ $order->created_at->format('Y/m/d h:m') }}</td>
                            <td>{{ format_price($order->amount) }}</td>
                            <td>{!! $order->status->toHtml() !!}</td>
                            <td>
                                <a class="ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.orders.view', $order->id) }}">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">{{ __('No orders!') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <div class="ps-pagination">
            {!! $orders->links() !!}
        </div>
    </div>
@endsection
