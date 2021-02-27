<div id="app">
    @if ($page->template === 'homepage')
        {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content, 'youtube'), $page) !!}
    @else
        {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, clean($page->content, 'youtube'), $page) !!}
    @endif
</div>
