<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
    <a href="javascript:;" class="dropdown-toggle dropdown-header-name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge badge-default"> {{ count($orders) }} </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li class="external">
            <h3>{!! trans('plugins/ecommerce::order.new_order_notice', ['count' => count($orders)]) !!}</h3>
            <a href="{{ route('orders.index') }}">{{ trans('plugins/ecommerce::order.view_all') }}</a>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: {{ count($orders) * 70 }}px;" data-handle-color="#637283">
                @foreach($orders as $order)
                    <li>
                        <a href="{{ route('orders.edit', $order->id) }}">
                            <span class="photo">
                                <img src="{{ \Botble\Base\Supports\Gravatar::image($order->address->email) }}" class="rounded-circle" alt="{{ $order->address->name }}">
                            </span>
                            <span class="subject"><span class="from"> {{ $order->address->name }} </span><span class="time">{{ $order->created_at->toDateTimeString() }} </span></span>
                            <span class="message"> {{ $order->address->phone }} - {{ $order->address->email }} </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</li>
