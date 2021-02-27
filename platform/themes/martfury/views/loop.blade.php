@foreach ($posts as $post)
    @if ($loop->first)
        <div class="ps-post">
            <div class="ps-post__thumbnail">
                <a class="ps-post__overlay" href="{{ $post->url }}"></a>
                <img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" />
            </div>
            <div class="ps-post__content">
                <div class="ps-post__top">
                    <div class="ps-post__meta">
                        @foreach($post->categories as $category)
                            <a href="{{ $category->url }}">{{ $category->name }}</a>
                        @endforeach
                    </div>
                    <a class="ps-post__title" href="{{ $post->url }}">{{ $post->name }}</a>
                </div>
                <div class="ps-post__bottom">
                    <p>{{ $post->created_at->format('M d, Y') }} @if ($post->author) {{ __('by') }} {{ $post->author->getFullName() }} @endif</p>
                </div>
            </div>
        </div>
    @endif
@endforeach

@if ($posts->count() > 1)
    <div class="row">
        @foreach ($posts as $post)
            @if (!$loop->first)
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 ">
                    <div class="ps-post">
                        <div class="ps-post__thumbnail">
                            <a class="ps-post__overlay" href="{{ $post->url }}"></a>
                            <img src="{{ RvMedia::getImageUrl($post->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" />
                        </div>
                        <div class="ps-post__content">
                            <div class="ps-post__top">
                                <div class="ps-post__meta">
                                    @foreach($post->categories as $category)
                                        <a href="{{ $category->url }}">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                                <a class="ps-post__title" href="{{ $post->url }}">{{ $post->name }}</a>
                            </div>
                            <div class="ps-post__bottom">
                                <p>{{ $post->created_at->format('M d, Y') }} @if ($post->author) {{ __('by') }} {{ $post->author->getFullName() }} @endif</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

<div class="ps-pagination">
    {!! $posts->withQueryString()->links() !!}
</div>
