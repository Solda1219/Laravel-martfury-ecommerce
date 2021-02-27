<?php

use Botble\Ecommerce\Models\StoreLocator;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Illuminate\Config\Repository;

if (!function_exists('array_equal')) {
    /**
     * @param array $first
     * @param array $second
     * @return bool
     */
    function array_equal(array $first, array $second)
    {
        if (count($first) != count($second)) {
            return false;
        }

        return !array_diff($first, $second) && !array_diff($second, $first);
    }
}

if (!function_exists('esc_sql')) {
    /**
     * @param string $string
     * @return string
     */
    function esc_sql($string)
    {
        return app('db')->getPdo()->quote($string);
    }
}

if (!function_exists('rv_get_image_list')) {
    /**
     * @param array $imagesList
     * @param array $sizes
     * @return array
     */
    function rv_get_image_list(array $imagesList, array $sizes)
    {
        $result = [];
        foreach ($sizes as $size) {
            $images = [];

            foreach ($imagesList as $url) {
                $images[] = RvMedia::getImageUrl($url, $size);
            }

            $result[$size] = $images;
        }

        return $result;
    }
}
if (!function_exists('get_ecommerce_setting')) {
    /**
     * @param string $key
     * @param null $default
     * @return string
     */
    function get_ecommerce_setting($key, $default = '')
    {
        return setting(config('plugins.ecommerce.general.prefix') . $key, $default);
    }
}

if (!function_exists('get_shipment_code')) {
    /**
     * @param int $shipmentId
     * @return Repository|mixed
     */
    function get_shipment_code($shipmentId)
    {
        return '#' . (config('plugins.ecommerce.order.default_order_start_number') + $shipmentId);
    }
}

if (!function_exists('get_primary_store_locator')) {
    /**
     * @return StoreLocator|mixed
     */
    function get_primary_store_locator()
    {
        $defaultStore = app(StoreLocatorInterface::class)->getFirstBy(['is_primary' => 1]);

        return $defaultStore ?? new StoreLocator;
    }
}

if (!function_exists('ecommerce_convert_weight')) {
    /**
     * @param int $weight
     * @return float|int
     */
    function ecommerce_convert_weight($weight)
    {
        switch (get_ecommerce_setting('store_weight_unit', 'g')) {
            case 'g':
                break;
            case 'kg':
                $weight = $weight * 1000;
                break;
        }

        return $weight;
    }
}

if (!function_exists('ecommerce_convert_width_height')) {
    /**
     * @param int $data
     * @return float|int
     */
    function ecommerce_convert_width_height($data)
    {
        switch (get_ecommerce_setting('store_width_height_unit', 'cm')) {
            case 'cm':
                break;
            case 'm':
                $data = $data * 100;
                break;
        }

        return $data;
    }
}

if (!function_exists('ecommerce_weight_unit')) {
    /**
     * @param bool $full
     * @return array|string
     */
    function ecommerce_weight_unit($full = false)
    {
        $unit = get_ecommerce_setting('store_weight_unit', 'g');

        if (!$full) {
            return $unit;
        }

        switch ($unit) {
            case 'g':
                $unit = __('grams');
                break;
            case 'kg':
                $unit = __('kilograms');
                break;
        }
        return $unit;
    }
}

if (!function_exists('ecommerce_width_height_unit')) {
    /**
     * @param bool $full
     * @return array|string
     */
    function ecommerce_width_height_unit($full = false)
    {
        $unit = get_ecommerce_setting('store_width_height_unit', 'cm');

        if (!$full) {
            return $unit;
        }

        switch ($unit) {
            case 'cm':
                $unit = __('centimeters');
                break;
            case 'm':
                $unit = __('meters');
                break;
        }

        return $unit;
    }
}

