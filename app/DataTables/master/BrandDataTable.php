<?php

namespace App\DataTables\Master;

use App\Models\Barang\Brand;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BrandDataTable extends DataTable
{
    use CustomDataTablePagination; // Gunakan trait

    private const TABLE_ID = 'brand-table';

    /**
     * Build the DataTable class.
     *
     * @param EloquentBuilder $query Results from query() method.
     */
    public function dataTable(EloquentBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('data', function ($row) {
                $imageUrl = $row->photo
                    ? url('storage/' . $row->photo)
                    : null;

                $name = $row->name;

                $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');

                
                if (!$imageUrl) {
                    return '<div class="flex items-center whitespace-nowrap">
                    <div class="h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800 text-sm">
                        ' . $initials . '
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">' . $name . '</p>
                        <p class="text-xs text-gray-500">#' . ($row->barang->count() ?? 'N/A') . ' Barang</p>
                    </div>
                </div>';
                }

                return '<div class="flex items-center whitespace-nowrap">
                <img src="' . $imageUrl . '" alt="Image" class="h-10 w-10 rounded-sm object-cover">
                <div class="ml-3">
                    <p class="font-medium text-gray-900">' . $name . '</p>
                    <p class="text-xs text-gray-500">#' . ($row->barang->count() ?? 'N/A') . ' Barang</p>
                </div>
            </div>';
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';

                $button .= '<button data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="edit-satuan-item-modal rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                $button .= '<button data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-modal rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-trash"></i></span></button>';

                $button .= '</div>';
                return $button;
            })

            ->filterColumn('name', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('name', 'like', "%" . $keyword . "%");
            })

            ->rawColumns(['data', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Brand $model): EloquentBuilder
    {
        return $model->newQuery()
        ->select([
            'brands.*',
        ])
        ->selectRaw('
            (SELECT COUNT(*) FROM barang WHERE barang.brand_id = brands.id) as barang_count
        ')
        ->orderByDesc('barang_count');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId(self::TABLE_ID) // Gunakan konstanta
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleMulti()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                // Gunakan method dari trait untuk buttons
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                // Gunakan method dari trait untuk drawCallback (custom pagination)
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Satuan Item..',
                    'paginate' => [
                        'first' => '<<',
                        'last' => '>>',
                        'next' => '>',
                        'previous' => '<',
                    ],
                ],
                // Gunakan method dari trait untuk initComplete (styling)
                'initComplete' => $this->getInitCompleteCallback(self::TABLE_ID),
            ]);
    }

    /**
     * Get columns definition for dataTable.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')
                ->title('No')
                ->searchable(true)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('name')
                ->title('Nama')
                ->searchable(true)
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('data')
                ->title('Nama')
                ->searchable(true)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('description')
                ->title('Deskripsi')
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900'),
            Column::make('barang_count')
                ->title('Jumlah Barang')
                ->searchable(false)
                ->orderable(true)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('action')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->title('ACTION')
                ->className('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis not-export'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Brand_' . date('YmdHis');
    }
}