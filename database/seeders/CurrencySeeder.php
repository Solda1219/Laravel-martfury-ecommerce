<?php

namespace Database\Seeders;

use Botble\Ecommerce\Models\Currency;
use Botble\Base\Supports\BaseSeeder;

class CurrencySeeder extends BaseSeeder
{
    public function run()
    {
        Currency::truncate();

        $currencies = [
            [
                'title'            => 'USD',
                'symbol'           => '$',
                'is_prefix_symbol' => true,
                'order'            => 0,
                'decimals'         => 2,
                'is_default'       => 1,
                'exchange_rate'    => 1,
            ],
            [
                'title'            => 'EUR',
                'symbol'           => '€',
                'is_prefix_symbol' => false,
                'order'            => 1,
                'decimals'         => 2,
                'is_default'       => 0,
                'exchange_rate'    => 1.18,
            ],
            [
                'title'            => 'VND',
                'symbol'           => '₫',
                'is_prefix_symbol' => false,
                'order'            => 2,
                'decimals'         => 0,
                'is_default'       => 0,
                'exchange_rate'    => 23203,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
