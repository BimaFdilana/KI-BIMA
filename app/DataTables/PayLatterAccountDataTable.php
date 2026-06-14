<?php

namespace App\DataTables;

use App\Models\PakDul\PayLatterAccount;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PayLatterAccountDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'paylatter-account-table';

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

                if (auth()->user()->can('edit.paylatter')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('paylatter.account.edit', $row->id) . '\'" 
                    title="Edit Account"
                    class="edit-paylatter-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>';
                }

                if (auth()->user()->can('view.paylatter')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('paylatter.account.show', $row->id) . '\'" 
                    title="Lihat Detail"
                    class="view-paylatter-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('user.name', function ($row) {
                return '<div class="flex items-center">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-sm w-10 h-10 flex items-center justify-center rounded-lg font-bold text-white mr-3 shadow-sm">
                        ' . strtoupper(substr($row->user->name ?? 'U', 0, 1)) . '
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">' . ($row->user->name ?? 'Unknown User') . '</div>
                        <div class="text-xs text-gray-500">' . ($row->user->email ?? '-') . '</div>
                    </div>
                </div>';
            })
            ->editColumn('toko.name', function ($row) {
                if (!$row->toko) return '<span class="text-gray-400 italic">No Toko</span>';
                return '<div class="flex items-center">
                    <i class="fad fa-store text-blue-500 mr-2"></i>
                    <span class="font-medium">' . $row->toko->name . '</span>
                </div>';
            })
            ->editColumn('credit_limit', function ($row) {
                return '<div class="text-right font-mono font-bold text-gray-900">
                    Rp ' . number_format($row->credit_limit, 0, ',', '.') . '
                </div>';
            })
            ->editColumn('used_credit', function ($row) {
                $percentage = $row->credit_limit > 0
                    ? min(100, ($row->used_credit / $row->credit_limit) * 100)
                    : 0;

                $barColor = $percentage > 80 ? 'from-red-400 to-red-600' : ($percentage > 50 ? 'from-yellow-400 to-yellow-600' : 'from-green-400 to-green-600');

                return '<div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider">
                        <span class="text-gray-500">Terpakai: Rp ' . number_format($row->used_credit, 0, ',', '.') . '</span>
                        <span class="' . ($percentage > 80 ? 'text-red-600' : 'text-gray-600') . '">' . number_format($percentage, 1) . '%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r ' . $barColor . ' h-full rounded-full transition-all duration-500" style="width: ' . $percentage . '%"></div>
                    </div>
                </div>';
            })
            ->editColumn('available_credit', function ($row) {
                return '<div class="text-right font-mono font-bold text-green-600">
                    Rp ' . number_format($row->available_credit, 0, ',', '.') . '
                </div>';
            })
            ->editColumn('status', function ($row) {
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800 border-green-200',
                    'suspended' => 'bg-red-100 text-red-800 border-red-200',
                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'closed' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];

                $statusIcons = [
                    'active' => 'fa-check-circle',
                    'suspended' => 'fa-ban',
                    'pending' => 'fa-clock',
                    'closed' => 'fa-times-circle',
                ];

                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border ' .
                    ($statusColors[$row->status] ?? 'bg-gray-100 text-gray-800 border-gray-200') . '">
                    <i class="fad ' . ($statusIcons[$row->status] ?? 'fa-question-circle') . ' mr-1.5"></i>
                    ' . strtoupper($row->status) . '
                </span>';
            })
            ->editColumn('payment_history_score', function ($row) {
                $score = $row->payment_history_score;
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $score) {
                        $stars .= '<i class="fas fa-star text-yellow-400"></i>';
                    } else {
                        $stars .= '<i class="far fa-star text-gray-300"></i>';
                    }
                }
                return '<div class="flex flex-col items-center">
                    <div class="flex space-x-0.5">' . $stars . '</div>
                    <span class="text-[10px] text-gray-500 mt-1 font-bold">' . $row->successful_payments . ' S / ' . $row->late_payments . ' L</span>
                </div>';
            })
            ->rawColumns(['user.name', 'toko.name', 'credit_limit', 'used_credit', 'available_credit', 'status', 'payment_history_score', 'action']);
    }

    public function query(PayLatterAccount $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['user', 'toko'])
            ->select('paylatter_accounts.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId(self::TABLE_ID)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleMulti()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> accounts',
                    'search' => '',
                    'searchPlaceholder' => 'Cari Akun Paylatter...',
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
            Column::make('user.name')->title('Pengguna'),
            Column::make('toko.name')->title('Toko'),
            Column::make('credit_limit')->title('Limit Kredit')->addClass('dt-right'),
            Column::make('used_credit')->title('Penggunaan')->addClass('dt-center'),
            Column::make('available_credit')->title('Sisa Limit')->addClass('dt-right'),
            Column::make('payment_history_score')->title('Skor Kredit')->addClass('dt-center'),
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
        return 'PayLatterAccount_' . date('YmdHis');
    }
}
