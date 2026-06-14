<?php

namespace App\DataTables;

use App\Models\Barang\BarangModel;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BarangDataTable extends DataTable
{

    use CustomDataTablePagination; // Gunakan trait
    protected $filterData;
    private const TABLE_ID = 'barang-table';

    public function setFilter($filter)
    {
        $this->filterData = $filter;
        return $this;
    }
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" name="barang_checkbox[]" class="barang-checkbox" value="' . $row->id . '">';
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';
                if (!$row->deleted_at) {
                    if (auth()->user()->can('edit.barang')) {
                        $button .= '<button tooltip title="Edit" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" onclick="window.location.href = \'' . route('barang.edit-barang', $row->sku) . '\'" class="edit-barang-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                    }
                    if (auth()->user()->can('view.barang')) {
                        $button .= '<button tooltip title="View" data-table="' . self::TABLE_ID . '" onclick="window.location.href = \'' . route('barang.show-barang', $row->sku) . '\'" type="button" class="view-barang-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-eye"></i></span></button>';
                    }
                    if (auth()->user()->can('delete.barang')) {
                        if ($row->barangki->count() === 0) {
                            $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-barang-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                            $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                        }
                    }
                } else {
                    if (auth()->user()->can('delete.barang')) {
                        $button .= '<button tooltip title="Restore" data-table="' . self::TABLE_ID . '"  data-id="' . $row->id . '" class="restore-barang-button rounded bg-green-100 text-green-600 hover:text-green-600 hover:bg-green-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-trash-restore"></i></span></button>';
                    } else {
                        $button .= '<div tooltip title="has been deleted" data-table="' . self::TABLE_ID . '"  data-id="' . $row->id . '" class="rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-times"></i></span></div>';
                    }
                }
                $button .= '</div> ';
                return $button;
            })


            ->addColumn('margin', function ($barang) {
                $margin = intval($barang->subcategory->margin); // Ubah ke bilangan bulat
                $color = '';
                if ($margin >= 80) {
                    $color = 'color: red;';
                } elseif ($margin >= 50) {
                    $color = 'color: yellow;';
                } else {
                    $color = 'color: green;';
                }
                $html = '<span style="' . $color . '">' . $margin . '%</span>';

                return $html;
            })
            ->addColumn('barang', function ($row) {
                $image = $row->images && $row->images->isNotEmpty() ? '<img src="' . url('storage/' . $row->images->first()->url) . '" alt="' . $row->name . '" class="h-10 w-10 rounded-sm object-cover">' : '<div class="h-10 w-10 rounded-sm bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-500">Image</div>';

                if ($row->deleted_at) {
                    $status = '<span class="rounded-sm bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Deleted</span>';
                } else {
                    $status = $row->is_active() ? '<span class="rounded-sm bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Active</span>' : '<span class="rounded-sm bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Inactive</span>';
                }

                $html =
                    '<div class="flex items-center space-x-3 whitespace-nowrap">
                            ' .
                    $image .
                    '
                            <div>
                                <div class="font-medium">' .
                    $row->name .
                    '</div>
                                <div class="mt-1">' .
                    $status .
                    '</div>
                            </div>
                         </div>';
                return $html;
            })

            ->addColumn('brand_name', function ($row) {
                return $row->brand ? $row->brand->name : '-';
            })
            ->addColumn('type_name', function ($row) {
                return $row->type ? $row->type->name : '-';
            })
            ->addColumn('category', function ($barang) {
                return $barang->subcategory ? $barang->subcategory->category->name : 'No Category';
            })
            ->addColumn('expiry', function ($barang) {
                return $barang->early_expiry_days . ' - ' . $barang->mid_expiry_days . ' - ' . $barang->late_expiry_days;
            })
            ->addColumn('status', function ($barang) {
                return $barang->is_active() ? 'active' : 'nonactive';
            })

            ->filterColumn('brand_name', function ($query, $keyword) {
                $query->whereHas('brand', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('type_name', function ($query, $keyword) {
                $query->whereHas('type', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('subcategory.category', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })

            // ✅ PERBAIKAN: Hapus whereHas('barang') karena sudah di model BarangModel
            ->filterColumn('expiry', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('early_expiry_days', 'like', '%' . $keyword . '%')
                        ->orWhere('mid_expiry_days', 'like', '%' . $keyword . '%')
                        ->orWhere('late_expiry_days', 'like', '%' . $keyword . '%');
                });
            })


            ->rawColumns(['checkbox', 'expiry', 'brand_name', 'type_name',  'action', 'category', 'margin', 'barang'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Barang\BarangModel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BarangModel $model): QueryBuilder
    {
        $query = $model
            ->newQuery()
            ->withTrashed()
            ->where(function ($query) {
                if ($this->filterData) {
                    if (isset($this->filterData['status']) && $this->filterData['status'] === 'deleted') {
                        $query->whereNotNull('deleted_at');
                    } else {
                        $query->where('status', $this->filterData['status'] ?? 'active');
                    }
                }
            })
            ->with(['brand', 'type', 'images', 'subcategory.category'])
            ->select('barang.*');

        if ($this->filterData && isset($this->filterData['type']) && $this->filterData['type'] > 0) {
            $query->whereHas('type', function ($query) {
                $query->where('id', $this->filterData['type']);
            });

            if ($query->count() === 0) {
                $query = $model->newQuery();
            }
        } elseif ($this->filterData && isset($this->filterData['more']) && $this->filterData['more'] === 'new_added') {
            $query->whereBetween('created_at', [now()->subDays(7), now()]);
        }

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
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
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Barang..',
                    'paginate' => [
                        'first' => '<<',
                        'last' => '>>',
                        'next' => '>',
                        'previous' => '<',
                    ],
                ],
                'initComplete' => $this->getInitCompleteCallback(self::TABLE_ID),
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->exportable(false)
                ->printable(false)
                ->title('<input type="checkbox" id="check-all" class="select-all">')
                ->footer('<input type="checkbox" id="check-all-footer">')
                ->width(50)
                ->addClass('dt-left not-export not-colvis'),
            Column::make('sku')
                ->width(60)
                ->addClass('dt-left font-medium text-gray-900'),
            Column::make('name')
                ->title('Nama Barang')
                ->visible(false)
                ->addClass('dt-left font-medium text-gray-900 not-colvis'),
            Column::computed('barang')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('dt-left font-medium text-gray-900 '),
            Column::make('brand_name')
                ->title('Brand')
                ->addClass('dt-center font-medium text-gray-900 not-export'),
            Column::make('type_name')
                ->title('Tipe')
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('description')
                ->title('Deskripsi')
                ->visible(
                    false
                )->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('category')
                ->title('Category')
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('early_expiry_days')
                ->title('Early Expiry')
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('mid_expiry_days')
                ->title('Mid Expiry')
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('late_expiry_days')
                ->title('Late Expiry')
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::computed('expiry')
                ->title('Kadaluarsa')
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::computed('status')
                ->title('Status')
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::computed('margin')
                ->title('Margin')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('dt-center whitespace-nowrap not-export not-colvis'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Barang_' . date('YmdHis');
    }
}
