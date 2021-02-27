<aside class="widget widget--blog widget--search">
    <form class="ps-form--widget-search" action="{{ route('public.search') }}" method="GET">
        <input class="form-control" name="q" value="{{ request()->query('q') }}" type="text" placeholder="{{ __('Search...') }}">
        <button type="submit"><i class="icon-magnifier"></i></button>
    </form>
</aside>
