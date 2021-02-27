<?php

namespace Botble\Faq\Tables;

use BaseHelper;
use Botble\Faq\Models\Faq;
use Html;
use Illuminate\Support\Facades\Auth;
use Botble\Faq\Repositories\Interfaces\FaqInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class FaqTable extends TableAbstract
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
     * FaqTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param FaqInterface $faqRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, FaqInterface $faqRepository)
    {
        $this->repository = $faqRepository;
        $this->setOption('id', 'table-plugins-faq');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['faq.edit', 'faq.destroy'])) {
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
            ->editColumn('question', function ($item) {
                if (!Auth::user()->hasPermission('faq.edit')) {
                    return $item->question;
                }

                return Html::link(route('faq.edit', $item->id), $item->question);
            })
            ->editColumn('category_id', function ($item) {
                return $item->category->name;
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
                return $this->getOperations('faq.edit', 'faq.destroy', $item);
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
            'faqs.id',
            'faqs.question',
            'faqs.created_at',
            'faqs.answer',
            'faqs.category_id',
            'faqs.status',
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
                'name'  => 'faqs.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'question'   => [
                'name'  => 'faqs.question',
                'title' => trans('plugins/faq::faq.question'),
                'class' => 'text-left',
            ],
            'category_id'   => [
                'name'  => 'faqs.category_id',
                'title' => trans('plugins/faq::faq.category'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'faqs.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'faq_categories.status',
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
        $buttons = $this->addCreateButton(route('faq.create'), 'faq.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Faq::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('faq.deletes'), 'faq.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'faqs.question'   => [
                'title'    => trans('plugins/faq::faq.question'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'faqs.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}
