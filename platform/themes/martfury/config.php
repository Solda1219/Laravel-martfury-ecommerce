<?php

use Botble\Theme\Theme;

return [

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    |
    | Set up inherit from another if the file is not exists,
    | this is work with "layouts", "partials" and "views"
    |
    | [Notice] assets cannot inherit.
    |
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these event can be override by package config.
    |
    */

    'events' => [
        // Listen on event before render a theme,
        // this event should call to assign some assets,
        // breadcrumb template.
        'beforeRenderTheme' => function (Theme $theme) {
            // You may use this event to set up your assets.

            $version = '1.0.7';

            $theme->asset()->usePath()->add('linearicons', 'fonts/Linearicons/Linearicons/Font/demo-files/demo.css');
            $theme->asset()->usePath()->add('bootstrap-css', 'plugins/bootstrap/css/bootstrap.min.css');
            $theme->asset()->usePath()->add('owl-carousel-css', 'plugins/owl-carousel/assets/owl.carousel.min.css');
            $theme->asset()->usePath()
                ->add('owl-carousel-theme-css', 'plugins/owl-carousel/assets/owl.theme.default.min.css');
            $theme->asset()->usePath()->add('slick-css', 'plugins/slick/slick.css');
            $theme->asset()->usePath()->add('nouislider-css', 'plugins/nouislider/nouislider.min.css');
            $theme->asset()->usePath()->add('lightgallery-css', 'plugins/lightGallery/css/lightgallery.min.css');
            $theme->asset()->usePath()
                ->add('jquery-bar-rating-css', 'plugins/jquery-bar-rating/themes/fontawesome-stars.css');
            $theme->asset()->usePath()->add('select2-css', 'plugins/select2/css/select2.min.css');
            $theme->asset()->usePath()->add('fontawesome', 'plugins/font-awesome/css/font-awesome.min.css');
            $theme->asset()->usePath()->add('style', 'css/style.css', [], [], $version);
            $theme->asset()->usePath()->add('custom', 'css/custom.css', [], [], $version);

            $theme->asset()->container('footer')->usePath()->add('jquery', 'plugins/jquery-3.5.1.min.js');
            $theme->asset()->container('footer')->usePath()
                ->add('nouislider-js', 'plugins/nouislider/nouislider.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()->add('popper-js', 'plugins/popper.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('owl-carousel-js', 'plugins/owl-carousel/owl.carousel.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('bootstrap-js', 'plugins/bootstrap/js/bootstrap.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('matchHeight-js', 'plugins/jquery.matchHeight-min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('slick-js', 'plugins/slick/slick.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('jquery-bar-rating-js', 'plugins/jquery-bar-rating/jquery.barrating.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('slick-animation-js', 'plugins/slick-animation.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('lightGallery-js', 'plugins/lightGallery/js/lightgallery.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('sticky-sidebar-js', 'plugins/sticky-sidebar/sticky-sidebar.min.js', ['jquery']);
            $theme->asset()->container('footer')->usePath()
                ->add('select2-js', 'plugins/select2/js/select2.min.js', ['jquery']);

            $theme->asset()->container('footer')->usePath()->add('main', 'js/main.js', ['jquery'], [], $version);
            $theme->asset()->container('footer')->usePath()
                ->add('backend', 'js/backend.js', ['jquery'], [], $version);

            $theme->asset()->container('footer')
                ->add('change-product-swatches', 'vendor/core/plugins/ecommerce/js/change-product-swatches.js',
                    ['jquery']);

            if (function_exists('shortcode')) {
                $theme->composer([
                    'index',
                    'page',
                    'post',
                    'ecommerce.product',
                    'ecommerce.products',
                    'ecommerce.product-category',
                    'ecommerce.product-tag',
                    'ecommerce.brand',
                    'ecommerce.search',
                ], function (\Botble\Shortcode\View\View $view) use ($theme, $version) {
                    $theme->asset()->container('footer')->usePath()
                        ->add('app-js', 'js/app.js', ['jquery', 'owl-carousel-js'], [], $version);
                    $view->withShortcodes();
                });
            }
        },
    ],
];
