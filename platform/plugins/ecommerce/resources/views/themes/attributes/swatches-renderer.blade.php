<div class="product-attributes"
     data-target="{{ route('public.web.get-variation-by-attributes', ['id' => $product->id]) }}">
    <ul>
        @foreach($attributeSets as $set)
            @if (view()->exists('plugins/ecommerce::themes.attributes._layouts.' . $set->display_layout))
                @include('plugins/ecommerce::themes.attributes._layouts.' . $set->display_layout, compact('selected'))
            @else
                @include('plugins/ecommerce::themes.attributes._layouts.dropdown', compact('selected'))
            @endif
        @endforeach
    </ul>
</div>
