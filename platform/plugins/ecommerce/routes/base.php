<?php

use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTag;

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/ecommerce', 'middleware' => 'auth'], function () {
        Route::get('settings', [
            'as'   => 'ecommerce.settings',
            'uses' => 'EcommerceController@getSettings',
        ]);

        Route::post('settings', [
            'as'         => 'ecommerce.settings.post',
            'uses'       => 'EcommerceController@postSettings',
            'permission' => 'ecommerce.settings',
        ]);

        Route::get('ajax/countries', [
            'as'         => 'ajax.countries.list',
            'uses'       => 'EcommerceController@ajaxGetCountries',
            'permission' => false,
        ]);

        Route::get('store-locators/form/{id?}', [
            'as'         => 'ecommerce.store-locators.form',
            'uses'       => 'EcommerceController@getStoreLocatorForm',
            'permission' => 'ecommerce.settings',
        ]);

        Route::post('store-locators/edit/{id}', [
            'as'         => 'ecommerce.store-locators.edit.post',
            'uses'       => 'EcommerceController@postUpdateStoreLocator',
            'permission' => 'ecommerce.settings',
        ]);

        Route::post('store-locators/create', [
            'as'         => 'ecommerce.store-locators.create',
            'uses'       => 'EcommerceController@postCreateStoreLocator',
            'permission' => 'ecommerce.settings',
        ]);

        Route::post('store-locators/delete/{id}', [
            'as'         => 'ecommerce.store-locators.destroy',
            'uses'       => 'EcommerceController@postDeleteStoreLocator',
            'permission' => 'ecommerce.settings',
        ]);

        Route::post('store-locators/update-primary-store', [
            'as'         => 'ecommerce.store-locators.update-primary-store',
            'uses'       => 'EcommerceController@postUpdatePrimaryStore',
            'permission' => 'ecommerce.settings',
        ]);

        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::resource('', 'ProductController')
                ->parameters(['' => 'product']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProductController@deletes',
                'permission' => 'posts.destroy',
            ]);

            Route::post('add-attribute-to-product/{id}', [
                'as'         => 'add-attribute-to-product',
                'uses'       => 'ProductController@postAddAttributeToProduct',
                'permission' => 'products.edit',
            ]);

            Route::post('delete-version/{id}', [
                'as'         => 'delete-version',
                'uses'       => 'ProductController@postDeleteVersion',
                'permission' => 'products.edit',
            ]);

            Route::post('add-version/{id}', [
                'as'         => 'add-version',
                'uses'       => 'ProductController@postAddVersion',
                'permission' => 'products.edit',
            ]);

            Route::get('get-version-form/{id}', [
                'as'         => 'get-version-form',
                'uses'       => 'ProductController@getVersionForm',
                'permission' => 'products.edit',
            ]);

            Route::post('update-version/{id}', [
                'as'         => 'update-version',
                'uses'       => 'ProductController@postUpdateVersion',
                'permission' => 'products.edit',
            ]);

            Route::post('generate-all-version/{id}', [
                'as'         => 'generate-all-versions',
                'uses'       => 'ProductController@postGenerateAllVersions',
                'permission' => 'products.edit',
            ]);

            Route::post('store-related-attributes/{id}', [
                'as'         => 'store-related-attributes',
                'uses'       => 'ProductController@postStoreRelatedAttributes',
                'permission' => 'products.edit',
            ]);

            Route::post('save-all-version/{id}', [
                'as'         => 'save-all-versions',
                'uses'       => 'ProductController@postSaveAllVersions',
                'permission' => 'products.edit',
            ]);

            Route::get('get-list-product-for-search/{id?}', [
                'as'         => 'get-list-product-for-search',
                'uses'       => 'ProductController@getListProductForSearch',
                'permission' => 'products.edit',
            ]);

            Route::get('get-relations-box/{id?}', [
                'as'         => 'get-relations-boxes',
                'uses'       => 'ProductController@getRelationBoxes',
                'permission' => 'products.edit',
            ]);

            Route::get('get-list-products-for-select', [
                'as'         => 'get-list-products-for-select',
                'uses'       => 'ProductController@getListProductForSelect',
                'permission' => 'products.index',
            ]);

            Route::post('create-product-when-creating-order', [
                'as'         => 'create-product-when-creating-order',
                'uses'       => 'ProductController@postCreateProductWhenCreatingOrder',
                'permission' => 'products.create',
            ]);

            Route::get('get-all-products-and-variations', [
                'as'         => 'get-all-products-and-variations',
                'uses'       => 'ProductController@getAllProductAndVariations',
                'permission' => 'products.index',
            ]);

            Route::post('update-order-by', [
                'as'         => 'update-order-by',
                'uses'       => 'ProductController@postUpdateOrderby',
                'permission' => 'products.edit',
            ]);
        });

        Route::group(['prefix' => 'product-categories', 'as' => 'product-categories.'], function () {
            Route::resource('', 'ProductCategoryController')
                ->parameters(['' => 'product_category']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProductCategoryController@deletes',
                'permission' => 'product-categories.destroy',
            ]);
        });

        Route::group(['prefix' => 'product-tags', 'as' => 'product-tag.'], function () {
            Route::resource('', 'ProductTagController')
                ->parameters(['' => 'product-tag']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProductTagController@deletes',
                'permission' => 'product-tag.destroy',
            ]);

            Route::get('all', [
                'as'         => 'all',
                'uses'       => 'ProductTagController@getAllTags',
                'permission' => 'product-tag.index',
            ]);
        });


        Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
            Route::resource('', 'BrandController')
                ->parameters(['' => 'brand']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'BrandController@deletes',
                'permission' => 'brands.destroy',
            ]);
        });

        Route::group(['prefix' => 'product-collections', 'as' => 'product-collections.'], function () {
            Route::resource('', 'ProductCollectionController')
                ->parameters(['' => 'product_collection']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProductCollectionController@deletes',
                'permission' => 'product-collections.destroy',
            ]);

            Route::get('get-list-product-collections-for-select', [
                'as'         => 'get-list-product-collections-for-select',
                'uses'       => 'ProductCollectionController@getListForSelect',
                'permission' => 'product-collections.index',
            ]);
        });

        Route::group(['prefix' => 'product-attribute-sets', 'as' => 'product-attribute-sets.'], function () {
            Route::resource('', 'ProductAttributeSetsController')
                ->parameters(['' => 'product_attribute_set']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ProductAttributeSetsController@deletes',
                'permission' => 'product-attribute-sets.destroy',
            ]);
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('', [
                'as'   => 'ecommerce.report.index',
                'uses' => 'ReportController@getIndex',
            ]);

            Route::get('revenue', [
                'as'         => 'ecommerce.report.revenue',
                'uses'       => 'ReportController@getRevenue',
                'permission' => 'ecommerce.report.index',
            ]);

            Route::get('top-selling-products', [
                'as'         => 'ecommerce.report.top-selling-products',
                'uses'       => 'ReportController@getTopSellingProducts',
                'permission' => 'ecommerce.report.index',
            ]);

            Route::get('dashboard-general-report', [
                'as'         => 'ecommerce.report.dashboard-widget.general',
                'uses'       => 'ReportController@getDashboardWidgetGeneral',
                'permission' => 'ecommerce.report.index',
            ]);
        });

        Route::group(['prefix' => 'flash-sales', 'as' => 'flash-sale.'], function () {
            Route::resource('', 'FlashSaleController')->parameters(['' => 'flash-sale']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FlashSaleController@deletes',
                'permission' => 'flash-sale.destroy',
            ]);
        });

    });
});

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers\Fronts', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get(SlugHelper::getPrefix(Product::class, 'products'), [
            'as'   => 'public.products',
            'uses' => 'PublicProductController@getProducts',
        ]);

        Route::get(SlugHelper::getPrefix(Brand::class, 'brands') . '/{slug}', [
            'uses' => 'PublicProductController@getBrand',
        ]);

        Route::get(SlugHelper::getPrefix(Product::class, 'products') . '/{slug}', [
            'uses' => 'PublicProductController@getProduct',
        ]);

        Route::get(SlugHelper::getPrefix(ProductCategory::class, 'product-categories') . '/{slug}', [
            'uses' => 'PublicProductController@getProductCategory',
        ]);

        Route::get(SlugHelper::getPrefix(ProductTag::class, 'product-tags') . '/{slug}', [
            'uses' => 'PublicProductController@getProductTag',
        ]);

        Route::get('currency/switch/{code?}', [
            'as'   => 'public.change-currency',
            'uses' => 'PublicEcommerceController@changeCurrency',
        ]);

        Route::get('product-variation/{id}', [
            'as'   => 'public.web.get-variation-by-attributes',
            'uses' => 'PublicProductController@getProductVariation',
        ]);

        Route::get('orders/tracking', [
            'as'   => 'public.orders.tracking',
            'uses' => 'PublicProductController@getOrderTracking',
        ]);
    });
});
