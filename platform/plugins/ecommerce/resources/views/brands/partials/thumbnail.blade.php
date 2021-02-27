<a href="{{ route('brands.edit', $item->id) }}" title="{{ $item->name }}">
    <img src="{{ RvMedia::getImageUrl($item->logo, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $item->name }}" width="80">
</a>
