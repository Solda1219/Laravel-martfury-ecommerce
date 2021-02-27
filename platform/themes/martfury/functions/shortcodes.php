<?php

use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Faq\Repositories\Interfaces\FaqCategoryInterface;

if (is_plugin_active('ecommerce')) {
    add_shortcode('featured-product-categories', __('Featured Product Categories'), __('Featured Product Categories'),
        function ($shortCode) {
            return Theme::partial('short-codes.featured-product-categories', [
                'title'       => $shortCode->title,
                'description' => $shortCode->description,
            ]);
        });

    shortcode()->setAdminConfig('featured-product-categories',
        Theme::partial('short-codes.featured-product-categories-admin-config'));

    add_shortcode('featured-products', __('Featured products'), __('Featured products'), function ($shortCode) {
        return Theme::partial('short-codes.featured-products', [
            'title' => $shortCode->title,
            'limit' => $shortCode->limit,
        ]);
    });

    shortcode()->setAdminConfig('featured-products', Theme::partial('short-codes.featured-products-admin-config'));

    add_shortcode('featured-brands', __('Featured Brands'), __('Featured Brands'), function ($shortCode) {
        return Theme::partial('short-codes.featured-brands', [
            'title' => $shortCode->title,
        ]);
    });

    shortcode()->setAdminConfig('featured-brands', Theme::partial('short-codes.featured-brands-admin-config'));

    add_shortcode('product-collections', __('Product Collections'), __('Product Collections'), function ($shortCode) {
        $productCollections = get_product_collections(['status' => BaseStatusEnum::PUBLISHED], [],
            ['id', 'name', 'slug'])->toArray();

        return Theme::partial('short-codes.product-collections', [
            'title'              => $shortCode->title,
            'productCollections' => $productCollections,
        ]);
    });

    shortcode()->setAdminConfig('product-collections',
        Theme::partial('short-codes.product-collections-admin-config'));

    add_shortcode('trending-products', __('Trending Products'), __('Trending Products'), function ($shortCode) {
        return Theme::partial('short-codes.trending-products', [
            'title' => $shortCode->title,
        ]);
    });

    shortcode()->setAdminConfig('trending-products', Theme::partial('short-codes.trending-products-admin-config'));

    add_shortcode('product-category-products', __('Product category products'), __('Product category products'),
        function ($shortCode) {
            $category = app(ProductCategoryInterface::class)->getFirstBy([
                'status' => BaseStatusEnum::PUBLISHED,
                'id'     => $shortCode->category_id,
            ], ['*'], [
                'children' => function ($query) {
                    $query->limit(3);
                },
            ]);

            if (!$category) {
                return null;
            }

            return Theme::partial('short-codes.product-category-products', compact('category'));
        });

    shortcode()->setAdminConfig('product-category-products',
        Theme::partial('short-codes.product-category-products-admin-config'));

    add_shortcode('flash-sale', __('Flash sale'), __('Flash sale'), function ($shortCode) {
        $flashSale = app(FlashSaleInterface::class)->getModel()
            ->where('id', $shortCode->flash_sale_id)
            ->notExpired()
            ->first();

        if (!$flashSale || !$flashSale->products()->count()) {
            return null;
        }

        return Theme::partial('short-codes.flash-sale', [
            'title'     => $shortCode->title,
            'flashSale' => $flashSale,
        ]);
    });

    shortcode()->setAdminConfig('flash-sale', function () {
        $flashSales = app(FlashSaleInterface::class)
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->notExpired()
            ->get();

        return Theme::partial('short-codes.flash-sale-admin-config', compact('flashSales'));
    });
}

if (is_plugin_active('simple-slider')) {
    add_filter(SIMPLE_SLIDER_VIEW_TEMPLATE, function () {
        return Theme::getThemeNamespace() . '::partials.short-codes.sliders';
    }, 120);
}

if (is_plugin_active('newsletter')) {
    add_shortcode('newsletter-form', __('Newsletter Form'), __('Newsletter Form'), function ($shortCode) {
        return Theme::partial('short-codes.newsletter-form', [
            'title'       => $shortCode->title,
            'description' => $shortCode->description,
        ]);
    });

    shortcode()->setAdminConfig('newsletter-form', Theme::partial('short-codes.newsletter-form-admin-config'));
}

add_shortcode('download-app', __('Download Apps'), __('Download Apps'), function ($shortCode) {
    return Theme::partial('short-codes.download-app', [
        'title'         => $shortCode->title,
        'description'   => $shortCode->description,
        'screenshot'    => $shortCode->screenshot,
        'androidAppUrl' => $shortCode->android_app_url,
        'iosAppUrl'     => $shortCode->ios_app_url,
    ]);
});

shortcode()->setAdminConfig('download-app', Theme::partial('short-codes.download-app-admin-config'));

if (is_plugin_active('faq')) {
    add_shortcode('faq', __('FAQs'), __('FAQs'), function ($shortCode) {
        $categories = app(FaqCategoryInterface::class)
            ->advancedGet([
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                ],
                'with'      => ['faqs'],
                'order_by'  => [
                    'faq_categories.order'      => 'ASC',
                    'faq_categories.created_at' => 'DESC',
                ],
            ]);

        return Theme::partial('short-codes.faq', [
            'title'      => $shortCode->title,
            'categories' => $categories,
        ]);
    });

    shortcode()->setAdminConfig('faq', Theme::partial('short-codes.faq-admin-config'));
}

add_shortcode('site-features', __('Site features'), __('Site features'), function () {
    return Theme::partial('short-codes.site-features');
});

if (is_plugin_active('contact')) {
    add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
        return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
    }, 120);
}

add_shortcode('google-map', __('Google map'), __('Custom map'), function ($shortCode) {
    return Theme::partial('short-codes.google-map', ['address' => $shortCode->content]);
});

shortcode()->setAdminConfig('google-map', Theme::partial('short-codes.google-map-admin-config'));

add_shortcode('youtube-video', __('Youtube video'), __('Add youtube video'), function ($shortCode) {
    return Theme::partial('short-codes.youtube-video', ['url' => $shortCode->content]);
});

shortcode()->setAdminConfig('youtube-video', Theme::partial('short-codes.youtube-video-admin-config'));

add_shortcode('contact-info-boxes', __('Contact info boxes'), __('Contact info boxes'), function ($shortCode) {
    return Theme::partial('short-codes.contact-info-boxes', ['title' => $shortCode->title]);
});

shortcode()->setAdminConfig('contact-info-boxes', Theme::partial('short-codes.contact-info-boxes-admin-config'));

add_shortcode('theme-ads', __('Theme ads'), __('Theme ads'), function ($shortCode) {
    $ads = [];
    $attributes = $shortCode->toArray();

    for ($i = 1; $i < 5; $i++) {
        if (isset($attributes['key_' . $i])) {
            $ad = AdsManager::displayAds($attributes['key_' . $i]);
            if ($ad) {
                $ads[] = $ad;
            }
        }
    }

    $ads = array_filter($ads);

    return Theme::partial('short-codes.theme-ads', compact('ads'));
});

shortcode()->setAdminConfig('theme-ads', function () {
    $ads = app(AdsInterface::class)->getModel()
        ->where('status', BaseStatusEnum::PUBLISHED)
        ->notExpired()
        ->get();

    return Theme::partial('short-codes.theme-ads-admin-config', compact('ads'));
});

add_shortcode('coming-soon', __('Coming soon'), __('Coming soon'), function ($shortCode) {
    return Theme::partial('short-codes.coming-soon', [
        'time'  => $shortCode->time,
        'image' => $shortCode->image,
    ]);
});

shortcode()->setAdminConfig('coming-soon', Theme::partial('short-codes.coming-soon-admin-config'));
