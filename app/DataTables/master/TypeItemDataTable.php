<?php

namespace App\DataTables\Master;

use App\Models\Barang\Subcategory;
use App\Models\Barang\TypeItem;
use App\Traits\CustomDataTablePagination; // Import trait
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TypeItemDataTable extends DataTable
{
    use CustomDataTablePagination; // Gunakan trait

    // Table ID konstanta untuk menghindari duplikasi
    private const TABLE_ID = 'type-item-table';

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
                $name = $row->name;

                $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');

                
                    return '<div class="flex items-center whitespace-nowrap">
                    <div class="">
                        <p class="font-medium text-gray-900">' . $name . '</p>
                        <p class="text-xs text-gray-500">#' . ($row->barang_count ?? 'N/A') . ' Barang</p>
                    </div>
                </div>';
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';

                $button .= '<button data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="edit-subcategory-modal rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                $button .= '<button data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-modal rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-trash"></i></span></button>';

                $button .= '</div>';
                return $button;
            })

            ->filterColumn('data', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('name', 'like', "%" . $keyword . "%");
            })

            ->rawColumns(['data', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TypeItem $model): EloquentBuilder
    {
        return $model->newQuery()
            ->select([
                'type_barang.*',
            ])
            ->selectRaw('
                (SELECT COUNT(*) FROM barang WHERE barang.type_id = type_barang.id) as barang_count
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
                    'searchPlaceholder' => 'Search Subkategori..',
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
                ->title('ID')
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900')
                ->width('100px'),
            Column::make('name')
                ->title('Nama')
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis')
                ->visible(false)
                ->width('250px'),
            Column::make('data')
                ->title('Nama')
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-export')
                ->searchable(true)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->width('250px'),
            Column::make('barang_count')
                ->title('Jumlah Barang')
                ->searchable(false)
                ->orderable(true)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('description')
                ->title('Deskripsi')
                ->searchable(true)
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('action')
                ->searchable(false)
                ->orderable(false)
                ->title('ACTION')
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis not-export')
                ->width('120px'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Category_' . date('YmdHis');
    }
}