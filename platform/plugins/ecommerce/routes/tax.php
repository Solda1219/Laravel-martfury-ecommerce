<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'taxes', 'as' => 'tax.'], function () {

            Route::resource('', 'TaxController')->parameters(['' => 'tax']);

            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TaxController@deletes',
                'permission' => 'tax.destroy',
            ]);
        });
    });
});

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers\Fronts', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('tax/get-tax-amount', [
            'as'   => 'public.tax.get-tax-amount',
            'uses' => 'PublicCheckoutController@getTaxAmount',
        ]);
    });
});
