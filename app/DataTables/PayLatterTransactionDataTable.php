<?php

namespace App\DataTables;

use App\Models\PakDul\PayLatterTransaction;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PayLatterTransactionDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'paylatter-transaction-table';

    public function setFilter($filter)
    {
        $this->filterData = $filter;
        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';

                if (auth()->user()->can('view.paylatter')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('paylatter.transaction.show', $row->id) . '\'" 
                    title="Lihat Detail"
                    class="view-paylatter-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                if ($row->status !== 'paid' && auth()->user()->can('edit.paylatter')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('paylatter.transaction.edit', $row->id) . '\'" 
                    title="Update Status"
                    class="edit-paylatter-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen-alt"></i>
                </button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('transaction_code', function ($row) {
                return '<div class="flex flex-col">
                    <span class="font-mono font-bold text-red-600">' . $row->transaction_code . '</span>
                    <span class="text-[10px] text-gray-500">' . $row->created_at->format('d M Y, H:i') . '</span>
                </div>';
            })
            ->editColumn('account.user.name', function ($row) {
                return '<div class="flex items-center">
                    <div class="bg-gray-100 text-gray-600 text-[10px] w-8 h-8 flex items-center justify-center rounded-full font-bold mr-2 border border-gray-200">
                        ' . strtoupper(substr($row->account->user->name ?? 'U', 0, 1)) . '
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-900 text-sm">' . ($row->account->user->name ?? 'Unknown') . '</span>
                        <span class="text-[10px] text-gray-500">' . ($row->account->toko->name ?? '-') . '</span>
                    </div>
                </div>';
            })
            ->editColumn('principal_amount', function ($row) {
                return '<div class="text-right font-bold text-gray-900">
                    Rp ' . number_format($row->principal_amount, 0, ',', '.') . '
                </div>';
            })
            ->editColumn('total_amount', function ($row) {
                $extra = $row->interest_amount + $row->penalty_amount;
                $extraHtml = $extra > 0 ? '<div class="text-[10px] text-red-500 font-bold">+' . number_format($extra, 0, ',', '.') . ' (Bunga/Denda)</div>' : '';
                return '<div class="text-right">
                    <div class="font-black text-gray-900 text-lg">Rp ' . number_format($row->total_amount, 0, ',', '.') . '</div>
                    ' . $extraHtml . '
                </div>';
            })
            ->editColumn('remaining_amount', function ($row) {
                $color = $row->remaining_amount > 0 ? 'text-orange-600' : 'text-green-600';
                return '<div class="text-right font-black ' . $color . '">
                    Rp ' . number_format($row->remaining_amount, 0, ',', '.') . '
                </div>';
            })
            ->editColumn('due_date', function ($row) {
                $isOverdue = $row->status !== 'paid' && $row->due_date < now();
                $color = $isOverdue ? 'text-red-600 font-black' : 'text-gray-700 font-medium';
                $icon = $isOverdue ? '<i class="fad fa-exclamation-triangle mr-1"></i>' : '<i class="fad fa-calendar-alt mr-1"></i>';

                return '<div class="flex flex-col items-end">
                    <span class="' . $color . '">' . $icon . $row->due_date->format('d M Y') . '</span>
                    <span class="text-[10px] text-gray-500 italic">' . $row->due_date->diffForHumans() . '</span>
                </div>';
            })
            ->editColumn('status', function ($row) {
                $statusColors = [
                    'paid' => 'bg-green-100 text-green-800 border-green-200',
                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'overdue' => 'bg-red-100 text-red-800 border-red-200',
                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];

                $statusIcons = [
                    'paid' => 'fa-check-double',
                    'pending' => 'fa-clock',
                    'overdue' => 'fa-exclamation-circle',
                    'cancelled' => 'fa-ban',
                ];

                return '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black border ' .
                    ($statusColors[$row->status] ?? 'bg-gray-100 text-gray-800 border-gray-200') . '">
                    <i class="fad ' . ($statusIcons[$row->status] ?? 'fa-question-circle') . ' mr-1.5"></i>
                    ' . strtoupper($row->status) . '
                </span>';
            })
            ->rawColumns(['transaction_code', 'account.user.name', 'principal_amount', 'total_amount', 'remaining_amount', 'due_date', 'status', 'action']);
    }

    public function query(PayLatterTransaction $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['account.user', 'account.toko'])
            ->select('paylatter_transactions.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId(self::TABLE_ID)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'desc')
            ->selectStyleMulti()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> transactions',
                    'search' => '',
                    'searchPlaceholder' => 'Cari Transaksi Paylatter...',
                    'paginate' => [
                        'first' => '<<',
                        'last' => '>>',
                        'next' => '>',
                        'previous' => '<',
                    ],
                ],
                'initComplete' => $this->getInitCompleteCallback(self::TABLE_ID),
                'processing' => true,
                'serverSide' => true,
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('transaction_code')->title('Kode Transaksi'),
            Column::make('account.user.name')->title('Pengguna / Toko'),
            Column::make('principal_amount')->title('Pokok')->addClass('dt-right'),
            Column::make('total_amount')->title('Total Tagihan')->addClass('dt-right'),
            Column::make('remaining_amount')->title('Sisa Tagihan')->addClass('dt-right'),
            Column::make('due_date')->title('Jatuh Tempo')->addClass('dt-right'),
            Column::make('status')->title('Status')->addClass('dt-center'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('dt-center whitespace-nowrap not-export not-colvis'),
        ];
    }

    protected function filename(): string
    {
        return 'PayLatterTransaction_' . date('YmdHis');
    }
}
