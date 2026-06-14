<?php

namespace App\DataTables;

use App\Models\Infaq\InfaqList;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InfaqListDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'infaq-list-table';

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

                if (auth()->user()->can('edit.infaq')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('infaq.list.edit', $row->id) . '\'" 
                    title="Edit Pos Infaq"
                    class="edit-infaq-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>';
                }

                if (auth()->user()->can('view.infaq')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('infaq.list.show', $row->id) . '\'" 
                    title="Lihat Detail"
                    class="view-infaq-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                if (auth()->user()->can('delete.infaq')) {
                    $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-infaq-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('name', function ($row) {
                $initials = $row->initials;
                $gradientColors = [
                    'bg-gradient-to-br from-blue-500 to-purple-600',
                    'bg-gradient-to-br from-green-500 to-teal-600',
                    'bg-gradient-to-br from-red-500 to-pink-600',
                    'bg-gradient-to-br from-yellow-500 to-orange-600',
                    'bg-gradient-to-br from-indigo-500 to-blue-600',
                ];
                $gradientClass = $gradientColors[$row->id % count($gradientColors)];

                return '<div class="flex items-center">
                    <div class="' . $gradientClass . ' text-sm w-10 h-10 flex items-center justify-center rounded-lg bg-white/20 font-bold text-white mr-3">
                        ' . $initials . '
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">' . $row->name . '</div>
                        <div class="text-sm text-gray-500">' . $row->category_label . '</div>
                    </div>
                </div>';
            })
            ->editColumn('category', function ($row) {
                $categoryColors = [
                    'operasional' => 'bg-blue-100 text-blue-800',
                    'sosial' => 'bg-green-100 text-green-800',
                    'pembangunan' => 'bg-purple-100 text-purple-800',
                    'bencana' => 'bg-red-100 text-red-800',
                    'umum' => 'bg-gray-100 text-gray-800',
                ];

                $categoryIcons = [
                    'operasional' => 'fa-cogs',
                    'sosial' => 'fa-hands-helping',
                    'pembangunan' => 'fa-building',
                    'bencana' => 'fa-exclamation-triangle',
                    'umum' => 'fa-list',
                ];

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ' .
                    ($categoryColors[$row->category] ?? 'bg-gray-100 text-gray-800') . '">
                    <i class="fad ' . ($categoryIcons[$row->category] ?? 'fa-tag') . ' mr-1.5"></i>
                    ' . $row->category_label . '
                </span>';
            })
            ->editColumn('dana_dibutuhkan', function ($row) {
                $totalDonations = $row->total_donations;
                $percentage = $row->dana_dibutuhkan > 0
                    ? min(100, ($totalDonations / $row->dana_dibutuhkan) * 100)
                    : 0;

                return '<div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="font-medium text-gray-700">Rp ' . number_format($totalDonations, 0, ',', '.') . '</span>
                        <span class="text-gray-500">Rp ' . number_format($row->dana_dibutuhkan, 0, ',', '.') . '</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all" style="width: ' . $percentage . '%"></div>
                    </div>
                    <div class="text-xs text-gray-500 text-right">' . number_format($percentage, 1) . '%</div>
                </div>';
            })
            ->addColumn('donors_count', function ($row) {
                return '<div class="text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800">
                        <i class="fad fa-users mr-1.5"></i>
                        <span class="font-semibold">' . $row->donors_count . '</span>
                    </div>
                </div>';
            })
            ->editColumn('is_active', function ($row) {
                $statusColors = [
                    1 => 'bg-green-100 text-green-800',
                    0 => 'bg-red-100 text-red-800',
                ];

                $statusIcons = [
                    1 => 'fa-check-circle',
                    0 => 'fa-times-circle',
                ];

                $statusLabels = [
                    1 => 'Aktif',
                    0 => 'Nonaktif',
                ];

                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ' .
                    ($statusColors[$row->is_active] ?? 'bg-gray-100 text-gray-800') . '">
                    <i class="fad ' . ($statusIcons[$row->is_active] ?? 'fa-question-circle') . ' mr-1.5"></i>
                    ' . ($statusLabels[$row->is_active] ?? 'Unknown') . '
                </span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->translatedFormat('l, d M Y') : '-';
            })
            ->filterColumn('category', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $categories = array_keys(InfaqList::getCategories());
                foreach ($categories as $category) {
                    if (str_contains($category, $keyword)) {
                        $query->where('category', $category);
                        break;
                    }
                }
            })
            ->rawColumns(['name', 'category', 'dana_dibutuhkan', 'donors_count', 'is_active', 'created_at', 'action']);
    }

    public function query(InfaqList $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->withCount(['infaqHistories as donors_count' => function ($query) {
                $query->where('status', 'completed')->distinct('user_id');
            }])
            ->select('infaq_lists.*');
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
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Pos Infaq..',
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
            Column::make('name')->title('Nama Pos Infaq'),
            Column::make('category')
                ->title('Kategori')
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('dana_dibutuhkan')
                ->title('Progress Donasi')
                ->searchable(false)
                ->orderable(false)
                ->addClass('dt-center'),
            Column::computed('donors_count')
                ->title('Donatur')
                ->searchable(false)
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('is_active')
                ->title('Status')
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('created_at')->title('Dibuat'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('dt-center whitespace-nowrap not-export not-colvis'),
        ];
    }

    protected function filename(): string
    {
        return 'InfaqList_' . date('YmdHis');
    }
}
