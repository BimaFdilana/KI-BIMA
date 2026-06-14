<?php

namespace App\DataTables\Master;

use App\Models\Barang\Subcategory;
use App\Traits\CustomDataTablePagination; // Import trait
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubCategoryDataTable extends DataTable
{
    use CustomDataTablePagination; // Gunakan trait

    // Table ID konstanta untuk menghindari duplikasi
    private const TABLE_ID = 'subcategory-table';

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
                        <p class="text-xs text-gray-500">#' . ($row->barang->count() ?? 'N/A') . ' Subcategory</p>
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
            ->addColumn('margin', function ($row) {
                $percentage = round($row->margin, 2);
                $color = null;
                $icon = null;
                if ($percentage < 0) {
                    $color = 'bg-red-100';
                    $textColor = 'text-red-800';
                    $icon = '<i class="fa-solid fa-arrow-trend-up mr-1"></i>' . $percentage . '%';
                } elseif ($percentage < 15) {
                    $color = 'bg-green-100';
                    $textColor = 'text-green-800';
                    $icon = '<i class="fa-solid fa-arrow-trend-up mr-1"></i>' . $percentage . '%';
                } elseif ($percentage < 50) {
                    $color = 'bg-yellow-100';
                    $textColor = 'text-yellow-800';
                    $icon = '<i class="fa-solid fa-arrow-trend-up mr-1"></i>' . $percentage . '%';
                } else {
                    $color = 'bg-red-100';
                    $textColor = 'text-red-800';
                    $icon = '<i class="fa-solid fa-arrow-trend-up mr-1"></i>' . $percentage . '%';
                }

                return '<span class="px-2 py-1 rounded ' . $color . ' ' . $textColor . '">' . $icon . '</span>';
            })
            ->addColumn('category', function ($row) {
                return '<span class="bg-red-100 text-red-600 px-2 py-1 rounded">' . $row->category->name . '</span>';
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

            ->filterColumn('margin', function ($query, $keyword) {
                $query->where('margin', 'like', "%" . $keyword . "%");
            })

            ->rawColumns(['data', 'margin', 'category', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Subcategory $model): EloquentBuilder
    {
        return $model->newQuery()
            ->select([
                'sub_categories.*',
                
            ])
            ->selectRaw('
                (SELECT COUNT(*) FROM barang WHERE barang.subcategory_id = sub_categories.id) as barang_count
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
            Column::make('category')
                ->title('Kategori')
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('barang_count')
                ->title('Jumlah Barang')
                ->searchable(false)
                ->orderable(true)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('margin')
                ->title('Margin')
                ->searchable(false)
                ->orderable(true)
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