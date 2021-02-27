<?php

namespace Botble\SimpleSlider\Tables;

use BaseHelper;
use Html;
use Illuminate\Support\Facades\Auth;
use Botble\SimpleSlider\Repositories\Interfaces\SimpleSliderItemInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class SimpleSliderItemTable extends TableAbstract
{
    /**
     * @var string
     */
    protected $type = self::TABLE_TYPE_SIMPLE;

    /**
     * @var string
     */
    protected $view = 'plugins/simple-slider::items';

    /**
     * @var SimpleSliderItemInterface
     */
    protected $repository;

    /**
     * SimpleSliderItemTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param SimpleSliderItemInterface $simpleSliderItemRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        SimpleSliderItemInterface $simpleSliderItemRepository
    )
    {
        $this->repository = $simpleSliderItemRepository;
        $this->setOption('id', 'simple-slider-items-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['simple-slider-item.edit', 'simple-slider-item.destroy'])) {
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
            ->editColumn('image', function ($item) {
                return view('plugins/simple-slider::partials.thumbnail', compact('item'))->render();
            })
            ->editColumn('title', function ($item) {
                if (!Auth::user()->hasPermission('simple-slider-item.edit')) {
                    return $item->title;
                }

                return Html::link('#', $item->title, [
                    'data-fancybox' => true,
                    'data-type'     => 'ajax',
                    'data-src'      => route('simple-slider-item.edit', $item->id),
                ]);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return view('plugins/simple-slider::partials.actions', compact('item'))->render();
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
            'simple_slider_items.id',
            'simple_slider_items.title',
            'simple_slider_items.image',
            'simple_slider_items.order',
            'simple_slider_items.created_at',
        ];

        $query = $model
            ->select($select)
            ->where('simple_slider_id', request()->route()->parameter('id'));

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $operation = $this->getOperationsHeading();
        $operation['operations']['width'] = '170px;';

        return [
                'id'         => [
                    'title' => trans('core/base::tables.id'),
                    'width' => '20px',
                ],
                'image'      => [
                    'title' => trans('core/base::tables.image'),
                    'class' => 'text-center',
                ],
                'title'      => [
                    'title' => trans('core/base::tables.title'),
                    'class' => 'text-left',
                ],
                'order'      => [
                    'title' => trans('core/base::tables.order'),
                    'class' => 'text-left order-column',
                ],
                'created_at' => [
                    'title' => trans('core/base::tables.created_at'),
                    'width' => '100px',
                ],
            ] + $operation;
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function actions()
    {
        return [];
    }
}
