@php
    Theme::layout('homepage')
@endphp

<div id="app">
    {!! do_shortcode('[simple-slider key="home-slider"][/simple-slider]') !!}
    {!! do_shortcode('[featured-product-categories title="Top Categories"][/featured-product-categories]') !!}
</div>
