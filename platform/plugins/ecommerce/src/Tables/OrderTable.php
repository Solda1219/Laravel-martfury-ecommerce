<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class OrderTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * OrderTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param OrderInterface $orderRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OrderInterface $orderRepository)
    {
        $this->repository = $orderRepository;
        $this->setOption('id', 'table-orders');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasPermission('orders.edit')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? $item->payment->status->toHtml() : '&mdash;';
            })
            ->editColumn('payment_method', function ($item) {
                return $item->payment->payment_channel->label() ? $item->payment->payment_channel->label() : '&mdash;';
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount, $item->currency_id);
            })
            ->editColumn('shipping_amount', function ($item) {
                return format_price($item->shipping_amount, $item->currency_id);
            })
            ->editColumn('user_id', function ($item) {
                return $item->user->name ?? $item->address->name;
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        if (EcommerceHelper::isTaxEnabled()) {
            $data = $data->editColumn('tax_amount', function ($item) {
                return format_price($item->tax_amount, $item->currency_id);
            });
        }

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('orders.edit', 'orders.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'ec_orders.id',
            'ec_orders.status',
            'ec_orders.user_id',
            'ec_orders.created_at',
            'ec_orders.amount',
            'ec_orders.tax_amount',
            'ec_orders.currency_id',
            'ec_orders.shipping_amount',
            'ec_orders.payment_id',
        ];

        $query = $model
            ->select($select)
            ->with(['user', 'payment'])
            ->where('ec_orders.is_finished', 1);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns = [
            'id'              => [
                'name'  => 'ec_orders.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'user_id'         => [
                'name'  => 'ec_orders.user_id',
                'title' => trans('plugins/ecommerce::order.customer_label'),
                'class' => 'text-left',
            ],
            'amount'          => [
                'name'  => 'ec_orders.amount',
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-center',
            ],
        ];

        if (EcommerceHelper::isTaxEnabled()) {
            $columns['tax_amount'] = [
                'name'  => 'ec_orders.amount',
                'title' => trans('plugins/ecommerce::order.tax_amount'),
                'class' => 'text-center',
            ];
        }

        $columns += [
            'shipping_amount' => [
                'name'  => 'ec_orders.shipping_amount',
                'title' => trans('plugins/ecommerce::order.shipping_amount'),
                'class' => 'text-center',
            ],
            'payment_method'  => [
                'name'  => 'ec_orders.id',
                'title' => trans('plugins/ecommerce::order.payment_method'),
                'class' => 'text-center',
            ],
            'payment_status'  => [
                'name'  => 'ec_orders.id',
                'title' => trans('plugins/ecommerce::order.payment_status_label'),
                'class' => 'text-center',
            ],
            'status'          => [
                'name'  => 'ec_orders.status',
                'title' => trans('core/base::tables.status'),
                'class' => 'text-center',
            ],
            'created_at'      => [
                'name'  => 'ec_orders.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];

        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('orders.create'), 'orders.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Order::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_orders.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => OrderStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', OrderStatusEnum::values()),
            ],
            'ec_orders.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 &&
            !$this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id')
        ) {
            return view('plugins/ecommerce::orders.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
