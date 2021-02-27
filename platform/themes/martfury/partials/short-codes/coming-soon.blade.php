<div class="ps-page__content">
    @if ($image)
        <img src="{{ RvMedia::getImageUrl($image) }}" alt="image">
    @endif
    <figure>
        <figcaption>{{ __('NEW STORE WE BE LAUNCHED IN') }}:</figcaption>
        <ul class="ps-countdown" data-time="{{ $time }}">
            <li><span class="days"></span>
                <p>{{ __('Days') }}</p>
            </li>
            <li><span class="hours"></span>
                <p>{{ __('Hours') }}</p>
            </li>
            <li><span class="minutes"></span>
                <p>{{ __('Minutes') }}</p>
            </li>
            <li><span class="seconds"></span>
                <p>{{ __('Seconds') }}</p>
            </li>
        </ul>
    </figure>
</div>
