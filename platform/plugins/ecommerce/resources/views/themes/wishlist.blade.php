<div class="title">
    <h2 class="customer-page-title">{{ __('Wishlist') }}</h2>
</div>
<br>
@if (auth('customer')->check())
    @if (count($wishlist) > 0 && $wishlist->count() > 0)
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Product') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                @foreach($wishlist as $item)
                    @if (!empty($item->product))
                        <tr>
                            <td>
                                <img alt="{{ $item->product->name }}" width="50" height="70" class="img-fluid" style="max-height: 75px" src="{{ RvMedia::getImageUrl($item->product->image, 'thumb', false, RvMedia::getDefaultImage()) }}">
                            </td>
                            <td><a href="{{ $item->product->url }}">{{ $item->product->name }}</a></td>

                            <td>
                                <a href="{{ route('public.wishlist.remove', $item->product_id) }}">{{ __('Remove') }}</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <p>{{ __('No item in wishlist!') }}</p>
    @endif
@else
    @if (Cart::instance('wishlist')->count())
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ __('Image') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach(Cart::instance('wishlist')->content() as $cartItem)
                    @php
                        $item = app(\Botble\Ecommerce\Repositories\Interfaces\ProductInterface::class)->findById($cartItem->id);
                    @endphp
                    @if (!empty($item))
                        <tr>
                            <td>
                                <img alt="{{ $item->name }}" width="50" height="70" class="img-fluid" style="max-height: 75px" src="{{ RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()) }}">
                            </td>
                            <td><a href="{{ $item->url }}">{{ $item->name }}</a></td>

                            <td>
                                <a href="{{ route('public.wishlist.remove', $item->id) }}">{{ __('Remove') }}</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>{{ __('No item in wishlist!') }}</p>
    @endif
@endif
