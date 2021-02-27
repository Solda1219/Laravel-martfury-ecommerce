<?php

use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\WishlistInterface;

if (!function_exists('is_added_to_wishlist')) {
    /**
     * @param int $productId
     * @return bool
     */
    function is_added_to_wishlist($productId)
    {
        if (!auth('customer')->check()) {
            return false;
        }

        return app(WishlistInterface::class)->count([
                'product_id'  => $productId,
                'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            ]) > 0;
    }
}

if (!function_exists('count_customer_addresses')) {
    /**
     * @return int
     */
    function count_customer_addresses()
    {
        if (!auth('customer')->check()) {
            return 0;
        }

        return app(AddressInterface::class)->count(['customer_id' => auth('customer')->user()->getAuthIdentifier()]);
    }
}

if (!function_exists('get_customer_addresses')) {
    /**
     * @return array
     */
    function get_customer_addresses()
    {
        if (!auth('customer')->check()) {
            return [];
        }

        return app(AddressInterface::class)->advancedGet([
            'condition' => [
                'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            ],
            'order_by'  => [
                'is_default' => 'DESC',
            ],
        ]);
    }
}

if (!function_exists('get_default_customer_address')) {
    /**
     * @return Address
     */
    function get_default_customer_address()
    {
        if (!auth('customer')->check()) {
            return null;
        }

        return app(AddressInterface::class)->getFirstBy([
            'is_default'  => 1,
            'customer_id' => auth('customer')->user()->getAuthIdentifier(),
        ]);
    }
}
