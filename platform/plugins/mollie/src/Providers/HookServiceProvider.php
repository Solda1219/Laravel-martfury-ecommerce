<?php

namespace Botble\Mollie\Providers;

use Botble\Payment\Enums\PaymentMethodEnum;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Mollie;
use OrderHelper;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerMollieMethod'], 17, 2);
        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithMollie'], 17, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 99);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['MOLLIE'] = MOLLIE_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 23, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MOLLIE_PAYMENT_METHOD_NAME) {
                $value = 'Mollie';
            }

            return $value;
        }, 23, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MOLLIE_PAYMENT_METHOD_NAME) {
                $value = Html::tag('span', PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label'])
                    ->toHtml();
            }

            return $value;
        }, 23, 2);
    }

    /**
     * @param string $settings
     * @return string
     * @throws Throwable
     */
    public function addPaymentSettings($settings)
    {
        return $settings . view('plugins/mollie::settings')->render();
    }

    /**
     * @param string $html
     * @param array $data
     * @return string
     */
    public function registerMollieMethod($html, array $data)
    {
        return $html . view('plugins/mollie::methods', $data)->render();
    }

    /**
     * @param Request $request
     * @param array $data
     */
    public function checkoutWithMollie(array $data, Request $request)
    {
        if ($request->input('payment_method') == MOLLIE_PAYMENT_METHOD_NAME) {
            $response = Mollie::api()->payments->create([
                'amount'      => [
                    'currency' => $request->input('currency'),
                    'value'    => number_format($request->input('amount'), 2),
                ],
                'description' => 'Order #' . $request->input('order_id'),
                'redirectUrl' => route('public.checkout.success', OrderHelper::getOrderSessionToken()),
                'webhookUrl'  => route('mollie.payment.callback'),
                'metadata'    => ['order_id' => $request->input('order_id')],
            ]);

            header('Location: ' . $response->getCheckoutUrl());
            exit;
        }

        return $data;
    }
}
