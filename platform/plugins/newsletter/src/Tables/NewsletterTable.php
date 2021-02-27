<?php

namespace Botble\Newsletter\Tables;

use BaseHelper;
use Botble\Newsletter\Enums\NewsletterStatusEnum;
use Botble\Newsletter\Models\Newsletter;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class NewsletterTable extends TableAbstract
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
     * NewsletterTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param NewsletterInterface $newsletterRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        NewsletterInterface $newsletterRepository
    ) {
        $this->repository = $newsletterRepository;
        $this->setOption('id', 'table-newsletters');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasPermission('newsletter.destroy')) {
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
            ->editColumn('name', function ($item) {
                return trim($item->name) ? trim($item->name) : '&mdash;';
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'newsletter.destroy', $item);
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
            'newsletters.id',
            'newsletters.email',
            'newsletters.name',
            'newsletters.created_at',
            'newsletters.status',
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
                'name'  => 'newsletters.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'email'      => [
                'name'  => 'newsletters.email',
                'title' => trans('core/base::tables.email'),
                'class' => 'text-left',
            ],
            'name'       => [
                'name'  => 'newsletters.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'newsletters.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'     => [
                'name'  => 'newsletters.status',
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
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, [], Newsletter::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('newsletter.deletes'), 'newsletter.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'newsletters.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'newsletters.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120|email',
            ],
            'newsletters.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => NewsletterStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(NewsletterStatusEnum::values()),
            ],
            'newsletters.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
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
