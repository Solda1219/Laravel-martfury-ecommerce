<?php

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Route::group(['namespace' => 'Theme\Martfury\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('ajax/products', 'MartfuryController@ajaxGetProducts')
            ->name('public.ajax.products');

        Route::get('ajax/featured-product-categories', 'MartfuryController@getFeaturedProductCategories')
            ->name('public.ajax.featured-product-categories');

        Route::get('ajax/trending-products', 'MartfuryController@ajaxGetTrendingProducts')
            ->name('public.ajax.trending-products');

        Route::get('ajax/featured-brands', 'MartfuryController@ajaxGetFeaturedBrands')
            ->name('public.ajax.featured-brands');

        Route::get('ajax/featured-products', 'MartfuryController@ajaxGetFeaturedProducts')
            ->name('public.ajax.featured-products');

        Route::get('ajax/top-rated-products', 'MartfuryController@ajaxGetTopRatedProducts')
            ->name('public.ajax.top-rated-products');

        Route::get('ajax/on-sale-products', 'MartfuryController@ajaxGetOnSaleProducts')
            ->name('public.ajax.on-sale-products');

        Route::get('ajax/cart', 'MartfuryController@ajaxCart')
            ->name('public.ajax.cart');

        Route::get('ajax/quick-view/{id}', 'MartfuryController@getQuickView')
            ->name('public.ajax.quick-view');

        Route::get('ajax/featured-posts', 'MartfuryController@ajaxGetFeaturedPosts')
            ->name('public.ajax.featured-posts');

        Route::get('ajax/related-products/{id}', 'MartfuryController@ajaxGetRelatedProducts')
            ->name('public.ajax.related-products');

        Route::get('ajax/product-reviews/{id}', 'MartfuryController@ajaxGetProductReviews')
            ->name('public.ajax.product-reviews');

        Route::get('ajax/search-products', 'MartfuryController@ajaxSearchProducts')
            ->name('public.ajax.search-products');

        Route::post('ajax/send-download-app-links', 'MartfuryController@ajaxSendDownloadAppLinks')
            ->name('public.ajax.send-download-app-links');

        Route::get('ajax/product-categories/products', 'MartfuryController@ajaxGetProductsByCategoryId')
            ->name('public.ajax.product-category-products');

        Route::get('ajax/get-product-categories', 'MartfuryController@ajaxGetProductCategories')
            ->name('public.ajax.get-product-categories');

        Route::get('ajax/get-flash-sale/{id}', 'MartfuryController@ajaxGetFlashSale')
            ->name('public.ajax.get-flash-sale');
    });
});

Theme::routes();

Route::group(['namespace' => 'Theme\Martfury\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('/', 'MartfuryController@getIndex')
            ->name('public.index');

        Route::get('sitemap.xml', 'MartfuryController@getSiteMap')
            ->name('public.sitemap');

        Route::get('{slug?}' . config('core.base.general.public_single_ending_url'), 'MartfuryController@getView')
            ->name('public.single');

    });
});
