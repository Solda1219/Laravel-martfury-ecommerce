<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\AvatarRequest;
use Botble\Ecommerce\Http\Requests\EditAccountRequest;
use Botble\Ecommerce\Http\Requests\UpdatePasswordRequest;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Payment\Enums\PaymentStatusEnum;
use EmailHandler;
use Exception;
use File;
use Hash;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OrderHelper;
use Response;
use RvMedia;
use SeoHelper;
use Theme;
use Throwable;

class PublicController extends Controller
{
    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;

    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * PublicController constructor.
     * @param CustomerInterface $customerRepository
     * @param ProductInterface $productRepository
     * @param AddressInterface $addressRepository
     * @param OrderInterface $orderRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     */
    public function __construct(
        CustomerInterface $customerRepository,
        ProductInterface $productRepository,
        AddressInterface $addressRepository,
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css');
        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery']);

        Theme::asset()
            ->container('footer')
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery']);
    }

    /**
     * @return Response
     */
    public function getOverview()
    {
        SeoHelper::setTitle(auth('customer')->user()->name);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Overview'), route('customer.overview'));

        return Theme::scope('ecommerce.customers.overview', [], 'plugins/ecommerce::themes.customers.overview')
            ->render();
    }

    /**
     * @return Response
     */
    public function getEditAccount()
    {
        SeoHelper::setTitle(__('Edit Account'));
        Theme::asset()
            ->add('datepicker-style',
                'vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',
                ['bootstrap']);
        Theme::asset()
            ->container('footer')
            ->add('datepicker-js',
                'vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                ['jquery']);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Account Details'), route('customer.edit-account'));

        return Theme::scope('ecommerce.customers.edit-account', [], 'plugins/ecommerce::themes.customers.edit-account')
            ->render();
    }

    /**
     * @param EditAccountRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postEditAccount(EditAccountRequest $request, BaseHttpResponse $response)
    {
        $this->customerRepository->createOrUpdate(
            $request->input(),
            [
                'id' => auth('customer')->user()->getAuthIdentifier(),
            ]);

        return $response
            ->setNextUrl(route('customer.edit-account'))
            ->setMessage(__('Update profile successfully!'));
    }

    /**
     * @return Response
     */
    public function getChangePassword()
    {
        SeoHelper::setTitle(__('Change Password'));

        Theme::breadcrumb()->add(__('Home'), url('/'))
            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope('ecommerce.customers.change-password', [],
            'plugins/ecommerce::themes.customers.change-password')->render();
    }

    /**
     * @param UpdatePasswordRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postChangePassword(UpdatePasswordRequest $request, BaseHttpResponse $response)
    {
        $currentUser = auth('customer')->user();

        if (!Hash::check($request->input('old_password'), $currentUser->getAuthPassword())) {
            return $response
                ->setError()
                ->setMessage(trans('acl::users.current_password_not_valid'));
        }

        $this->customerRepository->update(['id' => $currentUser->getAuthIdentifier()], [
            'password' => bcrypt($request->input('password')),
        ]);

        return $response->setMessage(trans('acl::users.password_update_success'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getListOrders(Request $request)
    {
        SeoHelper::setTitle(__('Orders'));

        $orders = $this->orderRepository->advancedGet([
            'condition' => [
                'user_id' => auth('customer')->user()->getAuthIdentifier(),
            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => (int)$request->input('page'),
            ],
        ]);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Orders'), route('customer.orders'));

        return Theme::scope('ecommerce.customers.orders.list', compact('orders'),
            'plugins/ecommerce::themes.customers.orders.list')->render();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getViewOrder($id)
    {
        SeoHelper::setTitle(__('Order detail :id', ['id' => get_order_code($id)]));

        $order = $this->orderRepository->getFirstBy(
            [
                'id'      => $id,
                'user_id' => auth('customer')->user()->getAuthIdentifier(),
            ],
            ['ec_orders.*'],
            [
                'address',
                'products',
            ]
        );

        if (!$order) {
            abort(404);
        }

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Order detail :id', ['id' => get_order_code($id)]),
            route('customer.orders.view', $id));

        return Theme::scope('ecommerce.customers.orders.view', compact('order'),
            'plugins/ecommerce::themes.customers.orders.view')->render();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function getCancelOder($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->getFirstBy([
            'id'      => $id,
            'user_id' => auth('customer')->user()->getAuthIdentifier(),
        ], ['*']);

        if (!$order) {
            abort(404);
        }

        if (!in_array($order->status, [PaymentStatusEnum::PENDING, OrderStatusEnum::PROCESSING])) {
            return $response->setError()
                ->setMessage(trans('plugins/ecommerce::order.customer.messages.cancel_error'));
        }

        $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::CANCELED], compact('id'));

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('customer_cancel_order')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'customer_cancel_order',
                $order->user->email ? $order->user->email : $order->address->email
            );
        }

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'cancel_order',
            'description' => __('Order is cancelled by custom :customer',
                ['customer' => auth('customer')->user()->name]),
            'order_id'    => $order->id,
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.customer.messages.cancel_success'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getListAddresses(Request $request)
    {
        SeoHelper::setTitle(__('Addresses'));

        $addresses = $this->addressRepository->advancedGet([
            'condition' => [
                'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            ],
            'order_by'  => [
                'is_default' => 'DESC',
            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => (int)$request->input('page'),
            ],
        ]);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Addresses'), route('customer.address'));

        return Theme::scope('ecommerce.customers.address.list', compact('addresses'),
            'plugins/ecommerce::themes.customers.address.list')->render();
    }

    /**
     * @return Response
     */
    public function getCreateAddress()
    {
        SeoHelper::setTitle(__('Create Address'));

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Create Address'), route('customer.address.create'));

        return Theme::scope('ecommerce.customers.address.create', [],
            'plugins/ecommerce::themes.customers.address.create')->render();
    }

    /**
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postCreateAddress(AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default') == 1) {
            $this->addressRepository->update([
                'is_default'  => 1,
                'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            ], ['is_default' => 0]);
        }

        $request->merge([
            'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            'is_default'  => $request->input('is_default', 0),
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        return $response
            ->setData([
                'id'   => $address->id,
                'html' => view('plugins/ecommerce::orders.partials.address-item',
                    compact('address'))->render(),
            ])
            ->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getEditAddress($id)
    {
        SeoHelper::setTitle(__('Edit Address #:id', ['id' => $id]));

        $address = $this->addressRepository->findOrFail($id);

        Theme::breadcrumb()->add(__('Home'), url('/'))
            ->add(__('Edit Address #:id', ['id' => $id]), route('customer.address.edit', $id));

        return Theme::scope('ecommerce.customers.address.edit', compact('address'),
            'plugins/ecommerce::themes.customers.address.edit')->render();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getDeleteAddress($id, BaseHttpResponse $response)
    {
        $this->addressRepository->deleteBy([
            'id'          => $id,
            'customer_id' => auth('customer')->user()->getAuthIdentifier(),
        ]);
        return $response->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postEditAddress($id, AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default')) {
            $this->addressRepository->update([
                'is_default'  => 1,
                'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            ], ['is_default' => 0]);
        }

        $address = $this->addressRepository->createOrUpdate($request->input(), [
            'id'          => $id,
            'customer_id' => auth('customer')->user()->getAuthIdentifier(),
        ]);

        return $response
            ->setData([
                'id'   => $address->id,
                'html' => view('plugins/ecommerce::orders.partials.address-item', compact('address'))
                    ->render(),
            ])
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws BindingResolutionException
     */
    public function getPrintOrder($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->user_id != auth('customer')->user()->getAuthIdentifier()) {
            return $response
                ->setError()
                ->setMessage(__('Order is not existed!'));
        }

        $invoice = OrderHelper::generateInvoice($order);

        return response()->make(File::get($invoice), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . File::basename($invoice) . '"',
        ]);
    }

    /**
     * @param AvatarRequest $request
     * @param ThumbnailService $thumbnailService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postAvatar(AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $account = auth('customer')->user();

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'customers');

            if ($result['error'] != false) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width, (int)$avatarData->height)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $account->avatar = $file->url;

            $this->customerRepository->createOrUpdate($account);

            return $response
                ->setMessage(trans('plugins/customer::dashboard.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
