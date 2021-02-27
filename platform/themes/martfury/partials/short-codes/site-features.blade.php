<div class="ps-site-features">
    <div class="ps-container">
        <div class="ps-block--site-features">
            @for ($i = 1; $i <= 5; $i++)
                @if (theme_option('feature_' . $i . '_title'))
                    <div class="ps-block__item">
                        <div class="ps-block__left"><i class="{{ theme_option('feature_' . $i . '_icon') }}"></i></div>
                        <div class="ps-block__right">
                            <h4>{{ theme_option('feature_' . $i . '_title') }}</h4>
                            <p>{{ theme_option('feature_' . $i . '_subtitle') }}</p>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
    </div>
</div>
