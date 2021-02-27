<div class="ps-contact-info">
    <div class="container">
        <div class="ps-section__header">
            <h3>{!! clean($title) !!}</h3>
        </div>
        <div class="ps-section__content">
            <div class="row">
                @for ($i = 1; $i <= 6; $i++)
                    @if (theme_option('contact_info_box_' . $i . '_title'))
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="ps-block--contact-info">
                                <h4>{{ theme_option('contact_info_box_' . $i . '_title') }}</h4>
                                <!-- <p><span class="d-block">{{ theme_option('contact_info_box_' . $i . '_subtitle') }}</span><span>{{ theme_option('contact_info_box_' . $i . '_details') }}</span></p> -->
                                <p><span class="d-block">contact@rafasshop.com</span><span>+85212345678</span></p>
                            </div>
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>
</div>
