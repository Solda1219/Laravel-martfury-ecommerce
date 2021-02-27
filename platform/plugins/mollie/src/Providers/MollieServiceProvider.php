<?php

namespace Botble\Mollie\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\ServiceProvider;

class MollieServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @throws FileNotFoundException
     */
    public function boot()
    {
        if (is_plugin_active('payment')) {
            $this->setNamespace('plugins/mollie')
                ->loadRoutes(['web'])
                ->loadAndPublishViews()
                ->publishAssets();

            $this->app->register(HookServiceProvider::class);

            $this->app->make('config')->set([
                'mollie.key' => get_payment_setting('api_key', MOLLIE_PAYMENT_METHOD_NAME),
            ]);
        }
    }
}
