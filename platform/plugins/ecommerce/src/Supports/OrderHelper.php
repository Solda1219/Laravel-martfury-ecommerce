<?php

namespace Botble\Ecommerce\Supports;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleInterface;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Cart;
use EcommerceHelper as EcommerceHelperFacade;
use EmailHandler;
use Exception;
use File;
use Html;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Str;
use Log;
use PDF;
use RvMedia;
use Throwable;

class OrderHelper
{
    /**
     * @param string $orderId
     * @param string $chargeId
     * @param bool $paymentDone
     * @return BaseModel|bool
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function processOrder($orderId, $chargeId = null)
    {
        $order = app(OrderInterface::class)->findById($orderId);

        if (!$order) {
            return false;
        }

        if ($chargeId) {
            $payment = app(PaymentInterface::class)->getFirstBy(['charge_id' => $chargeId]);

            if ($payment) {
                $order->payment_id = $payment->id;
                $order->save();
            }
        }

        Cart::instance('cart')->destroy();
        session()->forget('applied_coupon_code');

        session(['order_id' => $orderId]);

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('admin_new_order')) {
            $this->setEmailVariables($order);
            $mailer->sendUsingTemplate('admin_new_order', setting('admin_email'));
        }

        session(['order_id' => $order->id]);

        app(OrderHistoryInterface::class)->createOrUpdate([
            'action'      => 'create_order',
            'description' => trans('plugins/ecommerce::order.new_order_from', [
                'order_id' => get_order_code($order->id),
                'customer' => $order->user->name ? $order->user->name : $order->address->name,
            ]),
            'order_id'    => $order->id,
        ]);

        $this->sendOrderConfirmationEmail($order, true);

        foreach ($order->products as $orderProduct) {
            $product = $orderProduct->product->original_product;

            $flashSale = $product->flashSales()->first();
            if (!$flashSale) {
                continue;
            }

            $flashSale->products()->detach([$product->id]);
            $flashSale->products()->attach([
                $product->id => [
                    'price'    => $flashSale->pivot->price,
                    'quantity' => (int)$flashSale->pivot->quantity,
                    'sold'     => (int)$flashSale->pivot->sold + 1,
                ],
            ]);
        }

        return $order;
    }

    /**
     * @param Order $order
     * @return \Botble\Base\Supports\EmailHandler
     * @throws Throwable
     */
    public function setEmailVariables($order)
    {
        return EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'store_address'    => get_ecommerce_setting('store_address'),
                'store_phone'      => get_ecommerce_setting('store_phone'),
                'order_id'         => str_replace('#', '', get_order_code($order->id)),
                'order_token'      => $order->token,
                'customer_name'    => $order->user->name ? $order->user->name : $order->address->name,
                'customer_email'   => $order->user->email ? $order->user->email : $order->address->email,
                'customer_phone'   => $order->user->phone ? $order->user->phone : $order->address->phone,
                'customer_address' => $order->address->address . ', ' . $order->address->city . ', ' . $order->address->country_name . (EcommerceHelperFacade::isZipCodeEnabled() ? ', ' . $order->address->zip_code : ''),
                'product_list'     => view('plugins/ecommerce::emails.partials.order-detail',
                    compact('order'))->render(),
                'shipping_method'  => $order->shipping_method_name,
                'payment_method'   => $order->payment->payment_channel->label(),
            ]);
    }

    /**
     * @param Order $order
     * @param bool $saveHistory
     * @return boolean
     * @throws Throwable
     */
    public function sendOrderConfirmationEmail($order, $saveHistory = false)
    {
        try {
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_new_order')) {
                $this->setEmailVariables($order);

                EmailHandler::send(
                    $mailer->getTemplateContent('customer_new_order'),
                    $mailer->getTemplateSubject('customer_new_order'),
                    $order->user->email ? $order->user->email : $order->address->email
                );

                if ($saveHistory) {
                    app(OrderHistoryInterface::class)->createOrUpdate([
                        'action'      => 'send_order_confirmation_email',
                        'description' => trans('plugins/ecommerce::order.confirmation_email_was_sent_to_customer'),
                        'order_id'    => $order->id,
                    ]);
                }
            }

            return true;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return false;
    }

    /**
     * @param Order $order
     * @return string
     */
    public function generateInvoice($order)
    {
        $folderPath = storage_path('app/public');
        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath);
        }
        $invoice = $folderPath . '/invoice-order-' . get_order_code($order->id) . '.pdf';

        if (File::exists($invoice)) {
            return $invoice;
        }

        PDF::loadView('plugins/ecommerce::invoices.template', compact('order'))
            ->setPaper('a4')
            ->setWarnings(false)
            ->save($invoice);

        return $invoice;
    }

    /**
     * @param string $method
     * @param null $option
     * @return array|null|string
     */
    public function getShippingMethod($method, $option = null)
    {
        $name = null;
        switch ($method) {
            default:
                if ($option) {
                    $rule = app(ShippingRuleInterface::class)->findById($option);
                    if ($rule) {
                        $name = $rule->name;
                    }
                }

                if (empty($name)) {
                    $name = trans('plugins/ecommerce::order.default');
                }
                break;
        }
        return $name;
    }

    /**
     * @param OrderHistory $history
     * @return mixed
     */
    public function processHistoryVariables($history)
    {
        if (empty($history)) {
            return null;
        }

        $variables = [
            'order_id'  => Html::link(route('orders.edit', $history->order->id), get_order_code($history->order->id))
                ->toHtml(),
            'user_name' => $history->user_id === 0 ? trans('plugins/ecommerce::order.system') :
                ($history->user ? $history->user->getFullName() : ($history->order->user->name ?
                    $history->order->user->name :
                    $history->order->address->name
                )),
        ];

        $content = $history->description;

        foreach ($variables as $key => $value) {
            $content = str_replace('% ' . $key . ' %', $value, $content);
            $content = str_replace('%' . $key . '%', $value, $content);
            $content = str_replace('% ' . $key . '%', $value, $content);
            $content = str_replace('%' . $key . ' %', $value, $content);
        }

        return $content;
    }

    /**
     * @param string $token
     * @param string|array $data
     * @return bool
     */
    public function setOrderSessionData($token, $data): bool
    {
        if (!$token) {
            $token = $this->getOrderSessionToken();
        }

        $data = array_merge($this->getOrderSessionData($token), $data);
        session([md5('checkout_address_information_' . $token) => $data]);

        return true;
    }

    /**
     * @return string
     */
    public function getOrderSessionToken(): string
    {
        if (session()->has('tracked_start_checkout')) {
            $token = session()->get('tracked_start_checkout');
        } else {
            $token = md5(Str::random(40));
            session(['tracked_start_checkout' => $token]);
        }

        return $token;
    }

    /**
     * @param string|null $token
     * @return array|SessionManager|Store|mixed
     */
    public function getOrderSessionData($token = null)
    {
        if (!$token) {
            $token = $this->getOrderSessionToken();
        }

        $sessionData = [];
        $sessionKey = md5('checkout_address_information_' . $token);
        if (session()->has($sessionKey)) {
            $sessionData = session($sessionKey);
        }

        return $sessionData;
    }

    /**
     * @param string $token
     */
    public function clearSessions($token)
    {
        Cart::instance('cart')->destroy();
        session()->forget('applied_coupon_code');
        session()->forget('order_id');
        session()->forget(md5('checkout_address_information_' . $token));
        session()->forget('tracked_start_checkout');
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function handleAddCart($product, $request)
    {
        /**
         * With product attribute
         */
        $parentProduct = get_parent_product($product->id);
        $productAttributesString = '';
        if (get_product_attributes($product->id)->count() > 0) {
            $productAttributes = get_product_attributes($product->id);

            $productAttributesString .= '(';

            foreach ($productAttributes as $index => $attribute) {
                $productAttributesString .= $attribute->attribute_set_title . ': ' . $attribute->title;

                if ($index < count($productAttributes) - 1) {
                    $productAttributesString .= ', ';
                }
            }
            $productAttributesString .= ')';
        }

        /**
         * Add cart to session
         */
        Cart::instance('cart')->add(
            $product->id,
            $parentProduct->name,
            $request->input('qty', 1),
            $product->original_price,
            [
                'image'      => RvMedia::getImageUrl($product->image, 'thumb', false, $parentProduct->image),
                'attributes' => $productAttributesString,
                'taxRate'    => $parentProduct->tax->percentage,
                'extras'     => $request->input('extras', []),
            ]
        );

        /**
         * prepare data for response
         */
        $cartItems = [];
        foreach (Cart::instance('cart')->content() as $item) {
            array_push($cartItems, $item);
        }

        return $cartItems;
    }
}
