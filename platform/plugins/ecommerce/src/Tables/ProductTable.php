<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductTable extends TableAbstract
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
     * @var string
     */
    protected $exportClass = ProductExport::class;

    /**
     * ProductTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductInterface $productRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        $this->repository = $productRepository;
        $this->setOption('id', 'table-products');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['products.edit', 'products.destroy'])) {
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
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('products.edit')) {
                    return $item->name;
                }

                return Html::link(route('products.edit', $item->id), $item->name);
            })
            ->editColumn('image', function ($item) {
                if ($this->request()->input('action') == 'csv') {
                    return RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());
                }

                if ($this->request()->input('action') == 'excel') {
                    return RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage());
                }

                return view('plugins/ecommerce::products.partials.thumbnail', compact('item'))->render();
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('price', function ($item) {
                $price = format_price($item->front_sale_price);

                if ($item->front_sale_price != $item->price) {
                    $price .= ' <del class="text-danger">' . format_price($item->price) . '</del>';
                }

                return $price;
            })
            ->editColumn('sku', function ($item) {
                return $item->sku ? $item->sku : '&mdash;';
            })
            ->editColumn('order', function ($item) {
                return view('plugins/ecommerce::products.partials.sort-order', compact('item'))->render();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('products.edit', 'products.destroy', $item);
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
            'ec_products.id',
            'ec_products.name',
            'ec_products.order',
            'ec_products.created_at',
            'ec_products.status',
            'ec_products.sku',
            'ec_products.images',
            'ec_products.price',
            'ec_products.sale_price',
            'ec_products.sale_type',
            'ec_products.start_date',
            'ec_products.end_date',
        ];

        $query = $model
            ->select($select)
            ->where('is_variation', 0);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . '$(".editable").editable();';
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'ec_products.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image'      => [
                'name'  => 'ec_products.images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name'       => [
                'name'  => 'ec_products.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'price'        => [
                'name'  => 'ec_products.price',
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-left',
            ],
            'sku'        => [
                'name'  => 'ec_products.sku',
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-left',
            ],
            'order'      => [
                'name'  => 'ec_products.order',
                'title' => trans('core/base::tables.order'),
                'width' => '50px',
                'class' => 'text-center',
            ],
            'created_at' => [
                'name'  => 'ec_products.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status'     => [
                'name'  => 'ec_products.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('products.create'), 'products.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Product::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('products.deletes'), 'products.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_products.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_products.order'      => [
                'title'    => trans('core/base::tables.order'),
                'type'     => 'number',
                'validate' => 'required|min:0',
            ],
            'ec_products.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_products.created_at' => [
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
            return view('plugins/ecommerce::products.intro');
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
