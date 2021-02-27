<?php

Route::group(['namespace' => 'Botble\Mollie\Http\Controllers', 'middleware' => ['core']], function () {
    Route::post('mollie/payment/callback', [
        'as'   => 'mollie.payment.callback',
        'uses' => 'MollieController@paymentCallback',
    ]);
});
