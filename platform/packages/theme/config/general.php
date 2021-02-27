<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asset url path
    |--------------------------------------------------------------------------
    |
    | The path to asset, this config can be cdn host.
    | eg. http://cdn.domain.com
    |
    */

    'assetUrl' => '/',

    /*
    |--------------------------------------------------------------------------
    | Theme Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a theme when using a "Theme" class the default theme
    | will replace automatically.
    |
    */

    'themeDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Layout Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a layout when using a "Theme" class the default layout
    | will replace automatically.
    |
    */

    'layoutDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Path to lookup theme
    |--------------------------------------------------------------------------
    |
    | The root path contains themes collections.
    |
    */

    'themeDir' => 'themes',

    /*
    |--------------------------------------------------------------------------
    | A pieces of theme collections
    |--------------------------------------------------------------------------
    |
    | Inside a theme path we need to set up directories to
    | keep "layouts", "assets" and "partials".
    |
    */

    'containerDir' => [
        'layout'  => 'layouts',
        'asset'   => '',
        'partial' => 'partials',
        'view'    => 'views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    */

    'events'        => [

    ],
    'theme-options' => [
        'opt_name'              => 'theme-options',
        'use_cdn'               => true,
        'display_name'          => 'Theme Options',
        'display_version'       => '1.0.0',
        'page_title'            => 'Theme Options',
        'update_notice'         => true,
        'admin_bar'             => true,
        'menu_type'             => 'menu',
        'menu_title'            => 'Sample Options',
        'allow_sub_menu'        => true,
        'page_parent_post_type' => 'your_post_type',
        'customizer'            => true,
        'default_mark'          => '*',
        'hints'                 => [
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => [
                'color' => 'light',
            ],
            'tip_position'  => [
                'my' => 'top left',
                'at' => 'bottom right',
            ],
            'tip_effect'    => [
                'show' => [
                    'duration' => '500',
                    'event'    => 'mouseover',
                ],
                'hide' => [
                    'duration' => '500',
                    'event'    => 'mouseleave unfocus',
                ],
            ],
        ],
        'output'                => true,
        'output_tag'            => true,
        'settings_api'          => true,
        'cdn_check_time'        => '1440',
        'compiler'              => true,
        'page_permissions'      => 'manage_options',
        'save_defaults'         => true,
        'show_import_export'    => true,
        'show_options_object'   => false,
        'database'              => 'options',
        'transient_time'        => '3600',
        'network_sites'         => true,
    ],
];
