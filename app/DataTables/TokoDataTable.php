<?php

namespace App\DataTables;

use App\Models\Toko\TokoModel;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TokoDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'toko-table';

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

                if (auth()->user()->can('edit.toko')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('toko.edit', $row->id) . '\'" 
                    title="Edit Toko"
                    class="edit-toko-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>';
                }

                if (auth()->user()->can('view.toko')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('toko.show', $row->id) . '\'" 
                    title="Lihat Detail"
                    class="view-toko-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                if (auth()->user()->can('delete.toko')) {
                    if ($row->count() === 0) {
                        $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-barang-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                    }
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('status', function ($row) {
                $editorName = optional($row->editor)->name ?? '-';
                $editedAt = $row->updated_at
                    ? $row->updated_at->translatedFormat('l, d M Y')
                    : '-';

                $statusColors = [
                    'active' => 'bg-green-100 text-green-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'suspend' => 'bg-red-100 text-red-800',
                    'hasReview' => 'bg-orange-100 text-orange-800',
                ];

                return '
                    <button data-popover-target="popover-' . $row->id . '" tooltip="true" title="Diedit oleh: ' . $editorName . ', Diedit: ' . $editedAt . '" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ' .
                    ($statusColors[$row->status] ?? 'bg-gray-100 text-gray-800') . '">
                        ' . ucfirst($row->status) . '
                    </button>';
            })
            ->editColumn('token', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-100 text-blue-800">
                 <i class="bi bi-octagon-fill mr-1.5"></i>
                    ' . ($row->token ?? 0) . '
                </span>';
            })
            ->editColumn('owner_id', function ($row) {
                if (!$row->owner) {
                    return '<span class="text-gray-400">Owner tidak ditemukan</span>';
                }

                $ownerName = $row->owner->name;
                $employeeCount = $row->users_count ?? $row->users()->count();

                if (isset($row->owner->profile_photo_path) && $row->owner->profile_photo_path) {
                    $ownerImage = '<img src="' . $row->owner->profile_photo_path . '" alt="' . $row->owner->name . '" class="h-8 w-8 rounded-sm object-cover mr-3">';
                } else {
                    $initials = method_exists($row->owner, 'getInitialsAttribute')
                        ? $row->owner->initials
                        : strtoupper(substr($row->owner->name, 0, 2));

                    $gradientClass = method_exists($row->owner, 'getAvatarGradientClass')
                        ? $row->owner->getAvatarGradientClass()
                        : 'bg-gradient-to-br from-blue-500 to-purple-600';

                    $ownerImage = '<div class="' . $gradientClass . ' text-sm w-8 h-8 flex items-center justify-center rounded-sm bg-white/20 font-bold text-white mr-3">
                    ' . $initials . '
                </div>';
                }

                return '<div class="flex items-center">
                    ' . $ownerImage . '
                    <div>
                        <div class="font-medium">' . $ownerName . '</div>
                        <div class="text-sm text-gray-500">' . $employeeCount . ' karyawan</div>
                    </div>
                </div>';
            })
            ->filterColumn('owner_id', function ($query, $keyword) {
                $query->whereHas('owner', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if (str_contains($keyword, 'active') && !str_contains($keyword, 'inactive')) {
                    $query->where('status', 'active');
                } elseif (str_contains($keyword, 'pending')) {
                    $query->where('status', 'pending');
                } elseif (str_contains($keyword, 'suspend')) {
                    $query->where('status', 'suspend');
                } elseif (str_contains($keyword, 'hasreview')) {
                    $query->where('status', 'hasReview');
                }
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->translatedFormat('l, d M Y') : '-';
            })
            ->rawColumns(['status', 'token', 'owner_id', 'created_at', 'action']);
    }

    public function query(TokoModel $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with(['editor', 'owner'])
            ->withCount(['barangs', 'users']) // Use withCount for better performance
            ->select('toko.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId(self::TABLE_ID)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleMulti()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Toko..',
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
            Column::make('name')->title('Nama Toko'),
            Column::make('owner_id')
                ->title('Pemilik')
                ->searchable(false)
                ->printable(false)
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap not-export'),
            Column::make('owner.name')
                ->title('Pemilik')
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap not-colvis'),
            Column::make('address')->title('Alamat'),
            Column::make('token')
                ->title('Token')
                ->addClass('dt-center whitespace-nowrap')
                ->searchable(false),
            Column::make('status')
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
        return 'TokoPatner_' . date('YmdHis');
    }
}
