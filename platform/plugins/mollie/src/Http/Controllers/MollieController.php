<?php

namespace Botble\Mollie\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Services\Traits\PaymentTrait;
use Illuminate\Http\Request;
use Mollie;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Types\PaymentStatus;
use OrderHelper;
use Throwable;

class MollieController extends BaseController
{
    use PaymentTrait;

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function paymentCallback(Request $request, BaseHttpResponse $response)
    {
        try {
            $result = Mollie::api()->payments->get($request->input('id'));
        } catch (ApiException $exception) {
            return $response
                ->setError()
                ->setNextUrl(route('public.checkout.success', OrderHelper::getOrderSessionToken()))
                ->setMessage($exception->getMessage());
        }

        $status = PaymentStatusEnum::PENDING;

        switch ($result->status) {
            case PaymentStatus::STATUS_OPEN:
            case PaymentStatus::STATUS_AUTHORIZED:
                $status = PaymentStatusEnum::PENDING;
                break;
            case PaymentStatus::STATUS_PAID:
                $status = PaymentStatusEnum::COMPLETED;
                break;
            case PaymentStatus::STATUS_CANCELED:
            case PaymentStatus::STATUS_EXPIRED:
            case PaymentStatus::STATUS_FAILED:
                $status = PaymentStatusEnum::FAILED;
                break;
        }

        $this->storeLocalPayment([
            'amount'          => $result->amount->value,
            'currency'        => $result->amount->currency,
            'charge_id'       => $result->id,
            'payment_channel' => MOLLIE_PAYMENT_METHOD_NAME,
            'status'          => $status,
            'customer_id'     => auth('customer')->check() ? auth('customer')->user()->getAuthIdentifier() : null,
            'payment_type'    => 'direct',
            'order_id'        => $result->metadata->order_id,
        ]);

        OrderHelper::processOrder($result->metadata->order_id, $result->id);

        if (!$result->isPaid()) {
            return $response
                ->setError()
                ->setNextUrl(route('public.checkout.success', OrderHelper::getOrderSessionToken()))
                ->setMessage(__('Error when processing payment via Mollie!'));
        }

        return $response
            ->setNextUrl(route('public.checkout.success', OrderHelper::getOrderSessionToken()))
            ->setMessage(__('Checkout successfully!'));
    }
}
