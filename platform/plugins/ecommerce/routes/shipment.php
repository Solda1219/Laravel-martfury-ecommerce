<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'shipments', 'as' => 'ecommerce.shipments.'], function () {

            Route::get('edit/{id}', [
                'as'         => 'edit',
                'uses'       => 'ShipmentController@edit',
                'permission' => 'ecommerce.orders.edit',
            ]);

            Route::post('update-status/{id}', [
                'as'         => 'update-status',
                'uses'       => 'ShipmentController@postUpdateStatus',
                'permission' => 'ecommerce.orders.edit',
            ]);

            Route::post('update-cod-status/{id}', [
                'as'         => 'update-cod-status',
                'uses'       => 'ShipmentController@postUpdateCodStatus',
                'permission' => 'ecommerce.orders.edit',
            ]);
        });
    });
});
