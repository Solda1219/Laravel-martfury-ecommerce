<?php

use Botble\Ads\Repositories\Interfaces\AdsInterface;

if (!function_exists('generate_ads_key')) {
    /**
     * @return string
     */
    function generate_ads_key(): string
    {
        do {
            $key = strtoupper(Str::random(12));
        } while (app(AdsInterface::class)->count(['key' => $key]) > 0);

        return $key;
    }
}
