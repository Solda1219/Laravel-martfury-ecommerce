<?php

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Ecommerce\Models\ProductCategory;

register_page_template([
    'blog-sidebar' => __('Blog Sidebar'),
    'full-width'   => __('Full width'),
    'homepage'     => __('Homepage'),
    'coming-soon'  => __('Coming soon'),
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => __('Footer sidebar'),
    'description' => __('Widgets in footer of page'),
]);

register_sidebar([
    'id'          => 'bottom_footer_sidebar',
    'name'        => __('Bottom Footer sidebar'),
    'description' => __('Widgets in bottom footer'),
]);

add_action('init', function () {
    config([
        'filesystems.disks.public.root' => public_path('storage'),
        'filesystems.disks.public.url'  => str_replace('/index.php', '', url('storage')),
    ]);
}, 124);

RvMedia::addSize('medium', 790, 510)->addSize('small', 300, 300);

if (is_plugin_active('ecommerce')) {
    add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
        if (get_class($object) == ProductCategory::class && $context == 'advanced') {
            MetaBox::addMetaBox('additional_product_category_fields', __('Addition Information'), function () {
                $icon = null;
                $args = func_get_args();
                if (!empty($args[0])) {
                    $icon = MetaBox::getMetaData($args[0], 'icon', true);
                }

                return Theme::partial('product-category-fields', compact('icon'));
            }, get_class($object), $context);
        }
    }, 24, 2);

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == ProductCategory::class) {
            MetaBox::saveMetaBoxData($object, 'icon', $request->input('icon'));
        }
    }, 230, 3);

    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == ProductCategory::class) {
            MetaBox::saveMetaBoxData($object, 'icon', $request->input('icon'));
        }
    }, 231, 3);

    app()->booted(function () {
        ProductCategory::resolveRelationUsing('icon', function ($model) {
            return $model->morphOne(MetaBoxModel::class, 'reference')->where('meta_key', 'icon');
        });
    });
}

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.icons-field', [
    'name',
    'value'      => null,
    'attributes' => [],
]);

Form::component('themeBrand', Theme::getThemeNamespace() . '::partials.brands-field', [
    'name',
    'value'      => null,
    'attributes' => [],
]);

add_action('init', function () {
    EmailHandler::addTemplateSettings(Theme::getThemeName(), [
        'name'        => __('Theme emails'),
        'description' => __('Config email templates for theme'),
        'templates'   => [
            'download_app' => [
                'title'       => __('Download apps'),
                'description' => __('Send mail with links to download apps'),
                'subject'     => __('Download apps'),
                'can_off'     => true,
            ],
        ],
        'variables'   => [],
    ], 'themes');
}, 125);

if (is_plugin_active('ads')) {
    AdsManager::registerLocation('top-slider-image-1', __('Top Slider Image 1'))
        ->registerLocation('top-slider-image-2', __('Top Slider Image 2'))
        ->registerLocation('product-sidebar', __('Product sidebar'));
}
