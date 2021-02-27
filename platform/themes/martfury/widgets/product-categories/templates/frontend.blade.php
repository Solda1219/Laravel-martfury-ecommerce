<footer-product-categories-component name="{{ $config['name'] }}" url="{{ route('public.ajax.get-product-categories') }}?{{ http_build_query(['categories' => $config['categories']]) }}"></footer-product-categories-component>

