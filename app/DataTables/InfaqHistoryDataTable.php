<?php

namespace App\DataTables;

use App\Models\Infaq\InfaqHistory;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InfaqHistoryDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'infaq-history-table';

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

                if (auth()->user()->can('view.infaq')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('infaq.history.show', $row->id) . '\'" 
                    title="Lihat Detail"
                    class="view-infaq-history-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                if (auth()->user()->can('edit.infaq') && $row->can_change_status) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('infaq.history.edit', $row->id) . '\'" 
                    title="Edit Status"
                    class="edit-infaq-history-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>';
                }

                if (auth()->user()->can('delete.infaq') && $row->status === 'pending') {
                    $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-infaq-history-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('donor_name', function ($row) {
                $donorName = $row->donor_name ?? 'Hamba Allah';
                $userName = $row->user ? $row->user->name : null;

                if ($row->user && isset($row->user->profile_photo_path) && $row->user->profile_photo_path) {
                    $donorImage = '<img src="' . $row->user->profile_photo_path . '" alt="' . $userName . '" class="h-10 w-10 rounded-full object-cover mr-3">';
                } else {
                    $initials = $userName
                        ? strtoupper(substr($userName, 0, 2))
                        : strtoupper(substr($donorName, 0, 2));

                    $gradientColors = [
                        'bg-gradient-to-br from-blue-500 to-purple-600',
                        'bg-gradient-to-br from-green-500 to-teal-600',
                        'bg-gradient-to-br from-red-500 to-pink-600',
                        'bg-gradient-to-br from-yellow-500 to-orange-600',
                    ];
                    $gradientClass = $gradientColors[$row->id % count($gradientColors)];

                    $donorImage = '<div class="' . $gradientClass . ' text-sm w-10 h-10 flex items-center justify-center rounded-full bg-white/20 font-bold text-white mr-3">
                        ' . $initials . '
                    </div>';
                }

                return '<div class="flex items-center">
                    ' . $donorImage . '
                    <div>
                        <div class="font-medium text-gray-900">' . $donorName . '</div>
                        ' . ($userName && $userName !== $donorName ? '<div class="text-sm text-gray-500">' . $userName . '</div>' : '') . '
                    </div>
                </div>';
            })
            ->editColumn('infaq_list_id', function ($row) {
                if (!$row->infaqList) {
                    return '<span class="text-gray-400">Pos tidak ditemukan</span>';
                }

                return '<div class="flex items-center">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-sm w-8 h-8 flex items-center justify-center rounded-lg bg-white/20 font-bold text-white mr-2">
                        ' . $row->infaqList->initials . '
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">' . $row->infaqList->name . '</div>
                        <div class="text-xs text-gray-500">' . $row->infaqList->category_label . '</div>
                    </div>
                </div>';
            })
            ->editColumn('toko_id', function ($row) {
                if (!$row->toko) {
                    return '<span class="text-gray-400">-</span>';
                }

                return '<div class="flex items-center">
                    <i class="fad fa-store text-blue-500 mr-2"></i>
                    <span class="font-medium text-gray-900">' . $row->toko->name . '</span>
                </div>';
            })
            ->editColumn('amount', function ($row) {
                return '<div class="text-right">
                    <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-100 text-green-800 font-semibold">
                        <i class="fad fa-coins mr-2"></i>
                        Rp ' . number_format($row->amount, 0, ',', '.') . '
                    </div>
                </div>';
            })
            ->editColumn('payment_method', function ($row) {
                $methodColors = [
                    'cash' => 'bg-green-100 text-green-800',
                    'transfer' => 'bg-blue-100 text-blue-800',
                    'digital_wallet' => 'bg-purple-100 text-purple-800',
                    'qris' => 'bg-indigo-100 text-indigo-800',
                ];

                $methodIcons = [
                    'cash' => 'fa-money-bill-wave',
                    'transfer' => 'fa-university',
                    'digital_wallet' => 'fa-wallet',
                    'qris' => 'fa-qrcode',
                ];

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ' .
                    ($methodColors[$row->payment_method] ?? 'bg-gray-100 text-gray-800') . '">
                    <i class="fad ' . ($methodIcons[$row->payment_method] ?? 'fa-credit-card') . ' mr-1.5"></i>
                    ' . $row->payment_method_label . '
                </span>';
            })
            ->editColumn('status', function ($row) {
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'failed' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                ];

                $statusIcons = [
                    'pending' => 'fa-clock',
                    'completed' => 'fa-check-circle',
                    'failed' => 'fa-times-circle',
                    'cancelled' => 'fa-ban',
                ];

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ' .
                    ($statusColors[$row->status] ?? 'bg-gray-100 text-gray-800') . '">
                    <i class="fad ' . ($statusIcons[$row->status] ?? 'fa-question-circle') . ' mr-1.5"></i>
                    ' . $row->status_label . '
                </span>';
            })
            ->editColumn('created_at', function ($row) {
                return '<div>
                    <div class="font-medium text-gray-900">' . $row->created_at->translatedFormat('d M Y') . '</div>
                    <div class="text-xs text-gray-500">' . $row->created_at->translatedFormat('H:i') . '</div>
                </div>';
            })
            ->filterColumn('donor_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(donor_name) LIKE ?", ["%" . strtolower($keyword) . "%"])
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                        });
                });
            })
            ->filterColumn('infaq_list_id', function ($query, $keyword) {
                $query->whereHas('infaqList', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->filterColumn('toko_id', function ($query, $keyword) {
                $query->whereHas('toko', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $statuses = array_keys(InfaqHistory::getStatuses());
                foreach ($statuses as $status) {
                    if (str_contains($status, $keyword)) {
                        $query->where('status', $status);
                        break;
                    }
                }
            })
            ->rawColumns(['donor_name', 'infaq_list_id', 'toko_id', 'amount', 'payment_method', 'status', 'created_at', 'action']);
    }

    public function query(InfaqHistory $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with(['user', 'infaqList', 'toko', 'selling'])
            ->select('infaq_histories.*');
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
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Riwayat Infaq..',
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
            Column::make('donor_name')
                ->title('Donatur')
                ->searchable(true)
                ->orderable(false),
            Column::make('infaq_list_id')
                ->title('Pos Infaq')
                ->searchable(false)
                ->orderable(false),
            Column::make('infaq_list.name')
                ->title('Pos Infaq')
                ->visible(false)
                ->addClass('not-colvis'),
            Column::make('toko_id')
                ->title('Toko')
                ->searchable(false)
                ->orderable(false)
                ->addClass('dt-center'),
            Column::make('toko.name')
                ->title('Toko')
                ->visible(false)
                ->addClass('not-colvis'),
            Column::make('amount')
                ->title('Jumlah')
                ->searchable(false)
                ->addClass('dt-center'),
            Column::make('payment_method')
                ->title('Metode Bayar')
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('status')
                ->title('Status')
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('created_at')
                ->title('Tanggal')
                ->addClass('dt-center'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('dt-center whitespace-nowrap not-export not-colvis'),
        ];
    }

    protected function filename(): string
    {
        return 'InfaqHistory_' . date('YmdHis');
    }
}
