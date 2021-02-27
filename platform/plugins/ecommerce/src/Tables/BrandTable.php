<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class BrandTable extends TableAbstract
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
     * BrandTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param BrandInterface $brandRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BrandInterface $brandRepository)
    {
        $this->repository = $brandRepository;
        $this->setOption('id', 'table-brands');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['brands.edit', 'brands.destroy'])) {
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
                if (!Auth::user()->hasPermission('brands.edit')) {
                    return $item->name;
                }

                return Html::link(route('brands.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('logo', function ($item) {
                return view('plugins/ecommerce::brands.partials.thumbnail', compact('item'))->render();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('brands.edit', 'brands.destroy', $item);
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
            'ec_brands.id',
            'ec_brands.name',
            'ec_brands.created_at',
            'ec_brands.status',
            'ec_brands.is_featured',
            'ec_brands.logo',
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
            'id'          => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'name'        => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'logo'        => [
                'title' => trans('plugins/ecommerce::brands.logo'),
                'class' => 'text-left',
            ],
            'is_featured' => [
                'title' => trans('core/base::tables.is_featured'),
                'class' => 'text-left',
            ],
            'created_at'  => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
            'status'      => [
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
        $buttons = $this->addCreateButton(route('brands.create'), 'brands.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Brand::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('brands.deletes'), 'brands.destroy',
            parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_brands.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_brands.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_brands.created_at' => [
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
            return view('plugins/ecommerce::brands.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
