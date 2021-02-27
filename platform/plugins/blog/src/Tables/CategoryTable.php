<?php

namespace Botble\Blog\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Models\Category;
use Html;
use Illuminate\Support\Facades\Auth;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class CategoryTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $useDefaultSorting = false;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * CategoryTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CategoryInterface $categoryRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CategoryInterface $categoryRepository)
    {
        $this->repository = $categoryRepository;
        $this->setOption('id', 'table-categories');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['categories.edit', 'categories.destroy'])) {
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
            ->of($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('categories.edit')) {
                    return $item->name;
                }

                return Html::link(route('categories.edit', $item->id), $item->indent_text . ' ' . $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('updated_at', function ($item) {
                return BaseHelper::formatDate($item->updated_at);
            })
            ->editColumn('status', function ($item) {
                if ($this->request()->input('action') === 'excel') {
                    return $item->status->getValue();
                }
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return view('plugins/blog::categories.actions', compact('item'))->render();
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return collect(get_categories([]));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'       => [
                'name'  => 'name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'updated_at' => [
                'name'  => 'updated_at',
                'title' => trans('core/base::tables.updated_at'),
                'width' => '100px',
            ],
            'status'     => [
                'name'  => 'status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('categories.create'), 'categories.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Category::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('categories.deletes'), 'categories.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'categories.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'categories.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'categories.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}
