<?php

namespace App\DataTables;

use App\Models\Infaq\InfaqImage;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InfaqImageDataTable extends DataTable
{
    use CustomDataTablePagination;

    protected $filterData;
    private const TABLE_ID = 'infaq-image-table';

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
                    onclick="viewImage(\'' . asset($row->image_path) . '\')" 
                    title="Lihat Gambar"
                    class="view-image-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-eye"></i>
                </button>';
                }

                if (auth()->user()->can('edit.infaq')) {
                    $button .= '<button 
                    type="button"
                    onclick="window.location.href = \'' . route('infaq.image.edit', $row->id) . '\'" 
                    title="Edit Gambar"
                    class="edit-infaq-image-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-110 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>';
                }

                if (auth()->user()->can('delete.infaq')) {
                    $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-infaq-image-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('image_path', function ($row) {
                $imageUrl = asset($row->image_path);

                return '<div class="flex items-center space-x-3">
                    <div class="relative group">
                        <img src="' . $imageUrl . '" 
                             alt="Infaq Image" 
                             class="h-16 w-24 object-cover rounded-lg shadow-sm border border-gray-200 cursor-pointer transition-transform group-hover:scale-105"
                             onclick="viewImage(\'' . $imageUrl . '\')">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all flex items-center justify-center">
                            <i class="fad fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 truncate max-w-xs" title="' . $row->image_path . '">
                            ' . basename($row->image_path) . '
                        </div>
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
            ->editColumn('is_main', function ($row) {
                if ($row->is_main) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fad fa-star mr-1.5"></i>
                        Gambar Utama
                    </span>';
                } else {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fad fa-image mr-1.5"></i>
                        Gambar Tambahan
                    </span>';
                }
            })
            ->editColumn('created_at', function ($row) {
                return '<div>
                    <div class="font-medium text-gray-900">' . $row->created_at->translatedFormat('d M Y') . '</div>
                    <div class="text-xs text-gray-500">' . $row->created_at->translatedFormat('H:i') . '</div>
                </div>';
            })
            ->filterColumn('infaq_list_id', function ($query, $keyword) {
                $query->whereHas('infaqList', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->filterColumn('is_main', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if (str_contains($keyword, 'utama') || str_contains($keyword, 'main')) {
                    $query->where('is_main', true);
                } elseif (str_contains($keyword, 'tambahan') || str_contains($keyword, 'additional')) {
                    $query->where('is_main', false);
                }
            })
            ->rawColumns(['image_path', 'infaq_list_id', 'is_main', 'created_at', 'action']);
    }

    public function query(InfaqImage $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with(['infaqList'])
            ->select('infaq_image.*');
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
                    'searchPlaceholder' => 'Search Gambar Infaq..',
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
            Column::make('image_path')
                ->title('Gambar')
                ->searchable(false)
                ->orderable(false),
            Column::make('infaq_list_id')
                ->title('Pos Infaq')
                ->searchable(false)
                ->orderable(false),
            Column::make('infaq_list.name')
                ->title('Pos Infaq')
                ->visible(false)
                ->addClass('not-colvis'),
            Column::make('is_main')
                ->title('Tipe')
                ->addClass('dt-center whitespace-nowrap'),
            Column::make('created_at')
                ->title('Ditambahkan')
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
        return 'InfaqImage_' . date('YmdHis');
    }
}
