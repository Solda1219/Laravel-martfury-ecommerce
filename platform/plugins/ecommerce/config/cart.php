<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cart database settings
    |--------------------------------------------------------------------------
    |
    | Here you can set the connection that the cart should use when
    | storing and restoring a cart.
    |
    */

    'database' => [

        'connection' => null,

        'table' => 'cart',

    ],

    /*
    |--------------------------------------------------------------------------
    | Destroy the cart on user logout
    |--------------------------------------------------------------------------
    |
    | When this option is set to 'true' the cart will automatically
    | destroy all cart instances when the user logs out.
    |
    */

    'destroy_on_logout' => false,

    /*
    |--------------------------------------------------------------------------
    | Default number format
    |--------------------------------------------------------------------------
    |
    | This defaults will be used for the formatted numbers if you don't
    | set them in the method call.
    |
    */

    'format' => [

        'decimals' => 2,

        'decimal_point' => '.',

        'thousand_separator' => ',',

    ],

];
