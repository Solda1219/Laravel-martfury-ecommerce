<?php

theme_option()
    ->setField([
        'id'         => 'copyright',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Copyright'),
        'attributes' => [
            'name'    => 'copyright',
            'value'   => '© 2021 Martfury. All right reserved.',
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Change copyright'),
                'data-counter' => 250,
            ],
        ],
        'helper'     => __('Copyright on footer of site'),
    ])
    ->setField([
        'id'         => 'primary_font',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'googleFonts',
        'label'      => __('Primary font'),
        'attributes' => [
            'name'  => 'primary_font',
            'value' => 'Work Sans',
        ],
    ])
    ->setField([
        'id'         => 'primary_color',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Primary color'),
        'attributes' => [
            'name'  => 'primary_color',
            'value' => '#fcb800',
        ],
    ])
    ->setField([
        'id'         => 'secondary_color',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Secondary color'),
        'attributes' => [
            'name'  => 'secondary_color',
            'value' => '#222222',
        ],
    ])
    ->setField([
        'id'         => 'enable_newsletter_popup',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable newsletter popup?'),
        'attributes' => [
            'name'    => 'enable_newsletter_popup',
            'list'    => [
                'no'  => trans('core/base::base.no'),
                'yes' => trans('core/base::base.yes'),
            ],
            'value'   => 'yes',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'newsletter_image',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'mediaImage',
        'label'      => __('Image for newsletter popup'),
        'attributes' => [
            'name'  => 'newsletter_image',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'welcome_message',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Welcome message'),
        'attributes' => [
            'name'    => 'welcome_message',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Welcome message'),
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'hotline',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Hotline'),
        'attributes' => [
            'name'    => 'hotline',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Hotline'),
                'data-counter' => 30,
            ],
        ],
    ])
    ->setField([
        'id'         => 'address',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Address'),
        'attributes' => [
            'name'    => 'address',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Address'),
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'email',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'email',
        'label'      => __('Email'),
        'attributes' => [
            'name'    => 'email',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Email'),
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'payment_methods',
        'section_id' => 'opt-text-subsection-ecommerce',
        'type'       => 'mediaImages',
        'label'      => __('Accepted Payment methods'),
        'attributes' => [
            'name'       => 'payment_methods[]',
            'values'     => theme_option('payment_methods', []),
        ],
    ])
    ->setSection([
        'title'      => __('Site features'),
        'desc'       => __('Site features'),
        'id'         => 'opt-text-subsection-site-features',
        'subsection' => true,
        'icon'       => 'fa fa-cube',
    ])
    ->setSection([
        'title'      => __('Product features'),
        'desc'       => __('Product features'),
        'id'         => 'opt-text-subsection-product-features',
        'subsection' => true,
        'icon'       => 'fa fa-cube',
    ])
    ->setSection([
        'title'      => __('Contact info boxes'),
        'desc'       => __('Contact info boxes'),
        'id'         => 'opt-text-subsection-contact-info-boxes',
        'subsection' => true,
        'icon'       => 'fa fa-envelope',
    ]);

for ($i = 1; $i <= 5; $i++) {
    theme_option()
        ->setField([
            'id'         => 'feature_' . $i . '_title',
            'section_id' => 'opt-text-subsection-site-features',
            'type'       => 'text',
            'label'      => __('Feature title') . ' ' . $i,
            'attributes' => [
                'name'    => 'feature_' . $i . '_title',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'feature_' . $i . '_subtitle',
            'section_id' => 'opt-text-subsection-site-features',
            'type'       => 'text',
            'label'      => __('Feature subtitle') . ' ' . $i,
            'attributes' => [
                'name'    => 'feature_' . $i . '_subtitle',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'feature_' . $i . '_icon',
            'section_id' => 'opt-text-subsection-site-features',
            'type'       => 'themeIcon',
            'label'      => __('Feature icon') . ' ' . $i,
            'attributes' => [
                'name'    => 'feature_' . $i . '_icon',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ]);
}

for ($i = 1; $i <= 5; $i++) {
    theme_option()
        ->setField([
            'id'         => 'product_feature_' . $i . '_title',
            'section_id' => 'opt-text-subsection-product-features',
            'type'       => 'text',
            'label'      => __('Product feature title') . ' ' . $i,
            'attributes' => [
                'name'    => 'product_feature_' . $i . '_title',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'product_feature_' . $i . '_icon',
            'section_id' => 'opt-text-subsection-product-features',
            'type'       => 'themeIcon',
            'label'      => __('Product feature icon') . ' ' . $i,
            'attributes' => [
                'name'    => 'product_feature_' . $i . '_icon',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ]);
}

for ($i = 1; $i <= 6; $i++) {
    theme_option()
        ->setField([
            'id'         => 'contact_info_box_' . $i . '_title',
            'section_id' => 'opt-text-subsection-contact-info-boxes',
            'type'       => 'text',
            'label'      => __('Contact box title') . ' ' . $i,
            'attributes' => [
                'name'    => 'contact_info_box_' . $i . '_title',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'contact_info_box_' . $i . '_subtitle',
            'section_id' => 'opt-text-subsection-contact-info-boxes',
            'type'       => 'text',
            'label'      => __('Contact box subtitle') . ' ' . $i,
            'attributes' => [
                'name'    => 'contact_info_box_' . $i . '_subtitle',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'contact_info_box_' . $i . '_details',
            'section_id' => 'opt-text-subsection-contact-info-boxes',
            'type'       => 'text',
            'label'      => __('Contact box detail') . ' ' . $i,
            'attributes' => [
                'name'    => 'contact_info_box_' . $i . '_details',
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ]);
}

theme_option()
    ->setSection([
        'title'      => __('Social links'),
        'desc'       => __('Social links'),
        'id'         => 'opt-text-subsection-social-links',
        'subsection' => true,
        'icon'       => 'fa fa-share-alt',
    ]);

for ($i = 1; $i <= 5; $i++) {
    theme_option()
        ->setField([
            'id'         => 'social-name-' . $i,
            'section_id' => 'opt-text-subsection-social-links',
            'type'       => 'text',
            'label'      => __('Name') . ' ' . $i,
            'attributes' => [
                'name'    => 'social-name-' . $i,
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'social-icon-' . $i,
            'section_id' => 'opt-text-subsection-social-links',
            'type'       => 'themeBrand',
            'label'      => __('Icon') . ' ' . $i,
            'attributes' => [
                'name'    => 'social-icon-' . $i,
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'social-url-' . $i,
            'section_id' => 'opt-text-subsection-social-links',
            'type'       => 'text',
            'label'      => __('URL') . ' ' . $i,
            'attributes' => [
                'name'    => 'social-url-' . $i,
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id'         => 'social-color-' . $i,
            'section_id' => 'opt-text-subsection-social-links',
            'type'       => 'customColor',
            'label'      => __('Color') . ' ' . $i,
            'attributes' => [
                'name'    => 'social-color-' . $i,
                'value'   => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ]);
}
