<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCollection;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductCollectionTable extends TableAbstract
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
     * ProductCollectionTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductCollectionInterface $productCollectionRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        ProductCollectionInterface $productCollectionRepository
    ) {
        $this->repository = $productCollectionRepository;
        $this->setOption('id', 'table-product-collections');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission([
            'product-collections.edit',
            'product-collections.destroy',
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
                if (!Auth::user()->hasPermission('product-collections.edit')) {
                    return $item->name;
                }

                return Html::link(route('product-collections.edit', $item->id), $item->name);
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
                return $this->getOperations(
                    'product-collections.edit',
                    'product-collections.destroy',
                    $item
                );
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
            'ec_product_collections.id',
            'ec_product_collections.name',
            'ec_product_collections.image',
            'ec_product_collections.slug',
            'ec_product_collections.created_at',
            'ec_product_collections.status',
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
                'name'  => 'ec_product_collections.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'image'      => [
                'name'  => 'ec_product_collections.image',
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
                'class' => 'text-left',
            ],
            'name'       => [
                'name'  => 'ec_product_collections.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'slug'       => [
                'name'  => 'ec_product_collections.slug',
                'title' => trans('core/base::forms.slug'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'ec_product_collections.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
            'status'     => [
                'name'  => 'ec_product_collections.status',
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
        $buttons = $this->addCreateButton(route('product-collections.create'), 'product-collections.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, ProductCollection::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('product-collections.deletes'), 'product-collections.destroy',
            parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_product_collections.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_product_collections.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_product_collections.created_at' => [
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
            return view('plugins/ecommerce::product-collections.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
