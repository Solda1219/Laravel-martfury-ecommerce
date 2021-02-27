<div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5" data-width="100%"></div>

@php
Theme::set('footer', Html::script('https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v7.0&autoLogAppEvents=1', [
    'async'       => true,
    'defer'       => 'true',
    'crossorigin' => 'anonymous',
    'nonce'       => '3imGgJOo',
]))
@endphp
