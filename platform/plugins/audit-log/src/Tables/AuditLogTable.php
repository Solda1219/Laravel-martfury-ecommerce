<?php

namespace Botble\AuditLog\Tables;

use Botble\AuditLog\Models\AuditHistory;
use Illuminate\Support\Facades\Auth;
use Botble\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class AuditLogTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * AuditLogTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AuditLogInterface $auditLogRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AuditLogInterface $auditLogRepository)
    {
        $this->repository = $auditLogRepository;
        $this->setOption('id', 'table-audit-logs');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasPermission('audit-log.destroy')) {
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
            ->editColumn('action', function ($history) {
                return view('plugins/audit-log::activity-line', compact('history'))->render();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'audit-log.destroy', $item);
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
        $select = ['audit_histories.*'];
        $query = $model
            ->with(['user'])
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
                'name'  => 'audit_histories.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'action'     => [
                'name'  => 'audit_histories.action',
                'title' => trans('plugins/audit-log::history.action'),
                'class' => 'text-left',
            ],
            'user_agent' => [
                'name'  => 'audit_histories.user_agent',
                'title' => trans('plugins/audit-log::history.user_agent'),
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = [
            'empty' => [
                'link' => route('audit-log.empty'),
                'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() . ' ' . trans('plugins/audit-log::history.delete_all'),
            ],
        ];

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, AuditHistory::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('audit-log.deletes'), 'audit-log.destroy', parent::bulkActions());
    }
}
