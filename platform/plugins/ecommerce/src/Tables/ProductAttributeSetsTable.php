<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ProductAttributeSetsTable extends TableAbstract
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
     * ProductAttributeSetsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductAttributeSetInterface $productAttributeSetRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        ProductAttributeSetInterface $productAttributeSetRepository
    ) {
        $this->repository = $productAttributeSetRepository;
        $this->setOption('id', 'table-product-attribute-sets');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['product-attribute-sets.edit', 'product-attribute-sets.destroy'])) {
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
            ->editColumn('title', function ($item) {
                if (!Auth::user()->hasPermission('product-attribute-sets.edit')) {
                    return $item->name;
                }

                return Html::link(route('product-attribute-sets.edit', $item->id), $item->title);
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
                return $this->getOperations('product-attribute-sets.edit', 'product-attribute-sets.destroy', $item);
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
            'ec_product_attribute_sets.id',
            'ec_product_attribute_sets.created_at',
            'ec_product_attribute_sets.title',
            'ec_product_attribute_sets.slug',
            'ec_product_attribute_sets.order',
            'ec_product_attribute_sets.status',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-center',
            ],
            'title'      => [
                'title' => trans('core/base::tables.title'),
                'class' => 'text-left',
            ],
            'slug'       => [
                'title' => trans('core/base::tables.slug'),
                'class' => 'text-left',
            ],
            'order'      => [
                'title' => trans('core/base::tables.order'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
            'status'     => [
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
        $buttons = $this->addCreateButton(route('product-attribute-sets.create'), 'product-attribute-sets.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, ProductAttributeSet::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('product-attribute-sets.deletes'), 'product-attribute-sets.destroy',
            parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_product_attribute_sets.title'      => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_product_attribute_sets.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_product_attribute_sets.created_at' => [
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
            return view('plugins/ecommerce::product-attributes.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
