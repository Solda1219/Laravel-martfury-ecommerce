<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ReviewTable extends TableAbstract
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
     * ReviewTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ReviewInterface $reviewRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ReviewInterface $reviewRepository)
    {
        $this->repository = $reviewRepository;
        $this->setOption('id', 'table-reviews');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['review.edit', 'review.destroy'])) {
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
            ->editColumn('product_id', function ($item) {
                if (!empty($item->product)) {
                    return Html::link($item->product->url,
                        $item->product_name,
                        ['target' => '_blank']
                    );
                }
                return null;
            })
            ->editColumn('customer_id', function ($item) {
                return $item->user->name;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return view('plugins/ecommerce::reviews.partials.actions', compact('item'))->render();
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
            'ec_reviews.id',
            'ec_reviews.star',
            'ec_reviews.comment',
            'ec_reviews.product_id',
            'ec_reviews.customer_id',
            'ec_reviews.status',
            'ec_reviews.created_at',
        ];

        $query = $model
            ->select($select)
            ->with(['user', 'product']);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'          => [
                'name'  => 'ec_reviews.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'product_id'  => [
                'name'  => 'ec_reviews.product_id',
                'title' => trans('plugins/ecommerce::review.product'),
                'class' => 'text-left',
            ],
            'customer_id' => [
                'name'  => 'ec_reviews.customer_id',
                'title' => trans('plugins/ecommerce::review.user'),
                'class' => 'text-left',
            ],
            'star'        => [
                'name'  => 'ec_reviews.star',
                'title' => trans('plugins/ecommerce::review.star'),
                'class' => 'text-center',
            ],
            'comment'     => [
                'name'  => 'ec_reviews.comment',
                'title' => trans('plugins/ecommerce::review.comment'),
                'class' => 'text-left',
            ],
            'status'      => [
                'name'  => 'ec_reviews.status',
                'title' => trans('plugins/ecommerce::review.status'),
                'class' => 'text-center',
            ],
            'created_at'  => [
                'name'  => 'ec_reviews.created_at',
                'title' => trans('core/base::tables.created_at'),
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
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, [], Review::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('reviews.deletes'), 'review.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_reviews.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_reviews.created_at' => [
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
            return view('plugins/ecommerce::reviews.intro');
        }
        return parent::renderTable($data, $mergeData);
    }
}
