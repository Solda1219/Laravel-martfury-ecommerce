<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShipmentController extends BaseController
{
    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var ShipmentInterface
     */
    protected $shipmentRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * @var ShipmentHistoryInterface
     */
    protected $shipmentHistoryRepository;

    /**
     * @param OrderInterface $orderRepository
     * @param ShipmentInterface $shipmentRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     * @param ShipmentHistoryInterface $shipmentHistoryRepository
     */
    public function __construct(
        OrderInterface $orderRepository,
        ShipmentInterface $shipmentRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ShipmentHistoryInterface $shipmentHistoryRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->shipmentHistoryRepository = $shipmentHistoryRepository;
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        Assets::addStylesDirectly('vendor/core/plugins/ecommerce/css/ecommerce.css')
            ->addScriptsDirectly('vendor/core/plugins/ecommerce/js/shipment.js');

        $shipment = $this->shipmentRepository->findOrFail($id);
        page_title()->setTitle(trans('plugins/ecommerce::shipping.edit_shipping', ['code' => get_shipment_code($id)]));

        return view('plugins/ecommerce::shipments.edit', compact('shipment'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateStatus($id, Request $request, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $this->shipmentRepository->createOrUpdate(['status' => $request->input('status')], compact('id'));

        $this->shipmentHistoryRepository->createOrUpdate([
            'action'      => 'update_status',
            'description' => trans('plugins/ecommerce::shipping.changed_shipping_status', [
                'status' => ShippingStatusEnum::getLabel($request->input('status')),
            ]),
            'shipment_id' => $id,
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey() ?? 0,
        ]);

        switch ($request->input('status')) {
            case ShippingStatusEnum::DELIVERED:
                $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::COMPLETED],
                    ['id' => $shipment->order_id]);

                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'update_status',
                    'description' => trans('plugins/ecommerce::shipping.order_confirmed_by'),
                    'order_id'    => $shipment->order_id,
                    'user_id'     => Auth::user()->getKey() ?? 0,
                ]);
                break;
            case ShippingStatusEnum::CANCELED:
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'cancel_shipment',
                    'description' => trans('plugins/ecommerce::shipping.shipping_canceled_by'),
                    'order_id'    => $shipment->order_id,
                    'user_id'     => Auth::user()->getKey(),
                ]);
                break;
        }

        return $response->setMessage(trans('plugins/ecommerce::shipping.update_shipping_status_success'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateCodStatus($id, Request $request, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $this->shipmentRepository->createOrUpdate(['cod_status' => $request->input('status')], compact('id'));

        $this->shipmentHistoryRepository->createOrUpdate([
            'action'      => 'update_cod_status',
            'description' => trans('plugins/ecommerce::shipping.updated_cod_status_by', [
                'status' => ShippingCodStatusEnum::getLabel($request->input('status')),
            ]),
            'shipment_id' => $id,
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey() ?? 0,
        ]);

        return $response->setMessage(trans('plugins/ecommerce::shipping.update_cod_status_success'));
    }
}
