<?php

Route::group(['namespace' => 'Botble\Ads\Http\Controllers', 'middleware' => ['web']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'ads', 'as' => 'ads.'], function () {
            Route::resource('', 'AdsController')->parameters(['' => 'ads']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AdsController@deletes',
                'permission' => 'ads.destroy',
            ]);
        });
    });

    Route::get('ads-click/{key}', [
        'as'   => 'public.ads-click',
        'uses' => 'PublicController@getAdsClick',
    ]);

});
