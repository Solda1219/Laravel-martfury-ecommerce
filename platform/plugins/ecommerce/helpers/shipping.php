<?php

if (!function_exists('get_shipping_setting')) {
    /**
     * @param string $key
     * @param null $type
     * @param null $default
     * @return array
     */
    function get_shipping_setting($key, $type = null, $default = null)
    {
        if (empty($type)) {
            $key = config('plugins.ecommerce.shipping.settings.prefix') . $key;
            return setting($key, $default ? $default : config('plugins.ecommerce.shipping.' . $key));
        }

        $key = config('plugins.ecommerce.shipping.settings.prefix') . $type . '_' . $key;

        return setting($key, $default ? $default : config('plugins.ecommerce.shipping.' . $key));
    }
}
