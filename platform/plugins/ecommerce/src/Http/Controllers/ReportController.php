<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Charts\TimeChart;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Tables\Reports\TopSellingProductsTable;
use Botble\Payment\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ReportController extends BaseController
{
    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * EcommerceReportController constructor.
     * @param OrderInterface $order
     * @param ProductInterface $product
     * @param CustomerInterface $customer
     */
    public function __construct(
        OrderInterface $order,
        ProductInterface $product,
        CustomerInterface $customer
    ) {
        $this->orderRepository = $order;
        $this->productRepository = $product;
        $this->customerRepository = $customer;
    }

    /**
     * @param TopSellingProductsTable $topSellingProductsTable
     * @return Factory|View
     */
    public function getIndex(TopSellingProductsTable $topSellingProductsTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::reports.name'));

        Assets::addScripts(['blockui', 'equal-height', 'counterup'])
            ->addScriptsDirectly([
                'vendor/core/core/dashboard/js/dashboard.js',
                'vendor/core/plugins/ecommerce/js/report.js',
            ])
            ->addStylesDirectly('vendor/core/core/dashboard/css/dashboard.css');

        $count = [];
        $count['revenue'] = $this->orderRepository
            ->getModel()
            ->whereDate('ec_orders.created_at', now()->toDateString())
            ->join('payments', 'payments.order_id', '=', 'ec_orders.id')
            ->where('payments.status', PaymentStatusEnum::COMPLETED)
            ->sum('sub_total');

        $count['orders'] = $this->orderRepository
            ->getModel()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $count['products'] = $this->productRepository->count([
            'status'       => BaseStatusEnum::PUBLISHED,
            'is_variation' => false,
        ]);
        $count['customers'] = $this->customerRepository->count();

        $topSellingProducts = $topSellingProductsTable
            ->setAjaxUrl(route('ecommerce.report.top-selling-products'));

        return view('plugins/ecommerce::reports.index', compact('count', 'topSellingProducts'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getRevenue(Request $request, BaseHttpResponse $response)
    {
        $chartTime = null;
        $defaultRange = trans('plugins/ecommerce::reports.today');

        switch ($request->input('filter', 'week')) {
            case 'date':
                $startDate = now()->startOfDay()->toDateString();
                $endDate = now()->toDateString();
                $defaultRange = trans('plugins/ecommerce::reports.today');
                break;
            case 'month':
                $startDate = now()->startOfMonth()->toDateString();
                $endDate = now()->toDateString();
                $defaultRange = trans('plugins/ecommerce::reports.this_month');
                break;
            case 'year':
                $startDate = now()->startOfYear()->startOfDay()->toDateString();
                $endDate = now()->toDateString();
                $defaultRange = trans('plugins/ecommerce::reports.this_year');
                break;
            case 'week':
                $startDate = now()->subDays(7)->startOfDay()->toDateString();
                $endDate = now()->toDateString();
                $defaultRange = trans('plugins/ecommerce::reports.this_week');
                break;
        }

        $chartData = $this->orderRepository->getRevenueData($startDate, $endDate)->toArray();
        foreach ($chartData as &$data) {
            $data['formatted_date'] = Carbon::parse($data['date'])->format('d-m-Y');
            $data['formatted_revenue'] = format_price($data['revenue']);
        }
        if (!empty($chartData)) {
            $chartTime = (new TimeChart)->data($chartData)->setUseInlineJs(true);
        }

        return $response->setData(
            view('plugins/ecommerce::reports.partials.revenue', compact(
                'chartTime',
                'defaultRange'
            ))
                ->render()
        );
    }

    /**
     * @param TopSellingProductsTable $topSellingProductsTable
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function getTopSellingProducts(TopSellingProductsTable $topSellingProductsTable)
    {
        return $topSellingProductsTable->renderTable();
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getDashboardWidgetGeneral(BaseHttpResponse $response)
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $today = now()->toDateString();

        $processingOrders = $this->orderRepository
            ->getModel()
            ->where(['status' => OrderStatusEnum::PENDING])
            ->where('created_at', '>=', $startOfMonth)
            ->where('created_at', '<=', $today)
            ->count();

        $completedOrders = $this->orderRepository
            ->getModel()
            ->where(['status' => OrderStatusEnum::COMPLETED])
            ->where('created_at', '>=', $startOfMonth)
            ->where('created_at', '<=', $today)
            ->count();

        $revenue = $this->orderRepository->countRevenueByDateRange($startOfMonth, $today);

        $lowStockProducts = $this->productRepository
            ->getModel()
            ->where('with_storehouse_management', 1)
            ->where('quantity', '<', 2)
            ->where('quantity', '>', 0)
            ->count();

        $outOfStockProducts = $this->productRepository
            ->getModel()
            ->where('with_storehouse_management', 1)
            ->where('quantity', '<', 1)
            ->count();

        return $response
            ->setData(
                view('plugins/ecommerce::reports.widgets.general',
                    compact(
                        'processingOrders',
                        'revenue',
                        'completedOrders',
                        'outOfStockProducts',
                        'lowStockProducts'
                    )
                )
                    ->render()
            );
    }
}
