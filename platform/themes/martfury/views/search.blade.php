@php
    Theme::layout('blog-sidebar')
@endphp

@if ($posts->count() > 0)
    @include(Theme::getThemeNamespace() . '::views.loop', compact('posts'))
@endif
