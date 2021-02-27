<div class="product-attributes"
     data-target="{{ route('public.web.get-variation-by-attributes', ['id' => $product->id]) }}">
    @foreach($attributeSets as $set)
        @if (view()->exists(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts.' . $set->display_layout))
            @include(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts.' . $set->display_layout, compact('selected'))
        @else
            @include(Theme::getThemeNamespace(). '::views.ecommerce.attributes._layouts.dropdown', compact('selected'))
        @endif
    @endforeach
</div>
