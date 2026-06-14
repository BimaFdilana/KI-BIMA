<?php

namespace App\DataTables\Master;

use App\Models\Barang\SatuanConversion;
use App\Models\Barang\SatuanItem;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SatuanItemDataTable extends DataTable
{
    use CustomDataTablePagination; // Gunakan trait

    private const TABLE_ID = 'satuan-item-table';

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
            ->addColumn('name', function ($row) {
                $name = $row->name;
                
                    return '<div class="flex items-center whitespace-nowrap">
                    <div class="h-10 w-10 flex items-center justify-center rounded-sm from-gray-200 to-gray-100 bg-gradient-to-r text-gray-800 text-sm">
                        ' . $row->cut_name . '
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">' . $name . '</p>
                        <p class="text-xs text-gray-500">Level ' . ($row->level ?? 'N/A') . '</p>
                    </div>
                </div>';
            })

            ->addColumn('type', function ($data) {
                return "Level " . $data->level . " [" . $data->type . "]";
            })
            
            ->addColumn('selling', function ($data) {
                return '<span data-id="' . $data->id . '" data-selling="' . ($data->selling ? 'false' : 'true') . '" onclick="updateSatuanItem(this)" class="cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center ' . ($data->selling ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') . ' rounded-lg hover:text-' . ($data->selling ? 'green-600' : 'red-600') . ' hover:bg-' . ($data->selling ? 'green-200' : 'red-200') . ' text-sm font-medium px-2 py-1.5"><i class="fas ' . ($data->selling ? 'fa-check mr-2' : 'fa-times mr-2') . '"></i> ' . ($data->selling ? 'Ya' : 'Tidak') . '</span>';
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

            ->filterColumn('type', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('type', 'like', "%" . $keyword . "%");
            })


            ->rawColumns(['name', 'type', 'selling', 'conversion', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SatuanItem $model): EloquentBuilder
    {
        return $model->newQuery()
            ->select('*')
            ->orderBy('type', 'asc')  // Urutkan berdasarkan 'type' secara ascending
            ->orderBy('level', 'asc');  // Urutkan berdasarkan 'level' secara ascending
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
            Column::make('name')
                ->title('Nama')
                ->searchable(true)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900'),
            Column::make('type')
                ->title('Tipe')
                ->searchable(true)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('selling')
                ->title('Jual')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
                Column::make('description')
                ->title('Deskripsi')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 '),
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
        return 'SatuanItem_' . date('YmdHis');
    }
}