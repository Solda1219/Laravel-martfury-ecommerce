<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductCategoryTable extends TableAbstract
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
     * ProductCategoryTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductCategoryInterface $productCategoryRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        ProductCategoryInterface $productCategoryRepository
    ) {
        $this->repository = $productCategoryRepository;
        $this->setOption('id', 'table-product-categories');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission([
            'product-categories.edit',
            'product-categories.destroy',
        ])) {
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
                if (!Auth::user()->hasPermission('product-categories.edit')) {
                    return $item->name;
                }

                return Html::link(route('product-categories.edit', $item->id), $item->name);
            })
            ->editColumn('image', function ($item) {
                return Html::image(RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()),
                    $item->name, ['width' => 50]);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return view('plugins/ecommerce::product-categories.partials.actions', compact('item'))->render();
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
            'ec_product_categories.id',
            'ec_product_categories.name',
            'ec_product_categories.status',
            'ec_product_categories.order',
            'ec_product_categories.image',
            'ec_product_categories.created_at',
            'ec_product_categories.parent_id',
        ];

        $query = $model
            ->orderBy('ec_product_categories.parent_id', 'asc')
            ->orderBy('ec_product_categories.order', 'asc')
            ->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'ec_product_categories.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'image'      => [
                'name'  => 'ec_product_categories.image',
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
                'class' => 'text-left',
            ],
            'name'       => [
                'name'  => 'ec_product_categories.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'ec_product_categories.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
            'status'     => [
                'name'  => 'ec_product_categories.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('product-categories.create'), 'product-categories.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, ProductCategory::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('product-categories.deletes'), 'product-categories.destroy',
            parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_product_categories.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_product_categories.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_product_categories.created_at' => [
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
            return view('plugins/ecommerce::product-categories.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
