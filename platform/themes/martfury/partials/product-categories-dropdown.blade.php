@foreach ($categories as $category)
    <li @if ($category->children->count()) class="menu-item-has-children has-mega-menu" @endif>
        <a href="{{ $category->url }}">@if (count($category->icon->meta_value) > 0) <i class="{{ $category->icon->meta_value[0] }}"></i> @endif {{ $category->name }}</a>
        @if ($category->children->count())
            <span class="sub-toggle"></span>
            <div class="mega-menu">
                @foreach($category->children as $childCategory)
                    <div class="mega-menu__column">
                        @if ($childCategory->children->count())
                            <h4>{{ $childCategory->name }}<span class="sub-toggle"></span></h4>
                            <ul class="mega-menu__list">
                                @foreach($childCategory->children as $item)
                                    <li><a href="{{ $item->url }}">{{ $item->name }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $childCategory->url }}">{{ $childCategory->name }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </li>
@endforeach
