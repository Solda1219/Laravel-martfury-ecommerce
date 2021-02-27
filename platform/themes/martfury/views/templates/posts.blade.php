@if ($posts->count() > 0)
    @include(Theme::getThemeNamespace() . '::views.loop', compact('posts'))
@endif
