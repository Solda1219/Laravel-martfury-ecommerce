<ul class="breadcrumb">
    @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
        @if ($i != (count($crumbs) - 1))
            <li><a href="{{ $crumb['url'] }}">{!! $crumb['label'] !!}</a></li>
        @else
            <li>{!! $crumb['label'] !!}</li>
        @endif
    @endforeach
</ul>
