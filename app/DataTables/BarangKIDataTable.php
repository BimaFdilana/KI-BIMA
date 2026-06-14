<?php

namespace App\DataTables;

use App\Models\Barang\BarangKI;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields\Date;
use Yajra\DataTables\Html\Editor\Fields\Number;
use Yajra\DataTables\Html\Editor\Fields\Select;
use Yajra\DataTables\Html\Editor\Fields\Text;
use Yajra\DataTables\Services\DataTable;
use App\Traits\CustomDataTablePagination;
use App\Services\Barang\BarangKIService;

class BarangKIDataTable extends DataTable
{

    protected $convertService;
    protected $barangKIService;
    use CustomDataTablePagination; // Gunakan trait
    protected $filters;

    private const TABLE_ID = 'barang-table-ki';

    public function __construct(ConvertSatuanService $convertService, BarangKIService $barangKIService)
    {
        $this->convertService = $convertService;
        $this->barangKIService = $barangKIService;
    }

    public function setFilter($filter)
    {
        $this->filters = $filter;
        return $this;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('quantity_item', function ($barangKi) {
                $barangKIs = BarangKI::withTrashed()
                    ->where('barang_id', $barangKi->barang_id)
                    ->where('expired_time', $barangKi->expired_time)
                    ->where('deleted_at', null)
                    ->get();

                $convertToSmall = $this->convertService->convertBarangKeTerkecilDatatables($barangKIs);
                $result = $this->convertService->convertStock($barangKi, $convertToSmall['total']);

                // Handle case when result is a string (legacy format)
                if (is_string($result)) {
                    return '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">'
                        . $result . '</span>';
                }

                // Handle case when result is an array with formatted data
                if (!isset($result['formatted']) || empty($result['formatted'])) {
                    return '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-gray-100 text-gray-800">0</span>';
                }

                // Buat tombol untuk setiap item dengan warna sesuai indeks
                $buttons = [];
                $index = 0;

                // Loop melalui formatted items untuk tampilan
                foreach ($result['formatted'] as $formattedItem) {
                    // Cari level satuan yang sesuai
                    $satuanId = null;
                    foreach ($result['satuans'] as $id => $satuan) {
                        if (str_contains($formattedItem, $satuan['name'])) {
                            $satuanId = $id;
                            break;
                        }
                    }
                    $colorClass = 'bg-gray-100 text-gray-800'; // Default color
                    if ($satuanId !== null && isset($result['satuans'][$satuanId])) {
                        $level = $result['satuans'][$satuanId]['level'];
                        switch ($level) {
                            case 1:
                                $colorClass = 'bg-blue-100 text-blue-800';
                                break;
                            case 2:
                                $colorClass = 'bg-green-100 text-green-800';
                                break;
                            case 3:
                                $colorClass = 'bg-yellow-100 text-yellow-800';
                                break;
                            case 4:
                                $colorClass = 'bg-red-100 text-red-800';
                                break;
                            case 5:
                                $colorClass = 'bg-purple-100 text-purple-800';
                                break;
                        }
                    }

                    // Cari data satuan yang sesuai
                    $satuanId = null;
                    $satuanName = null;
                    $hargaBeli = 0;
                    $hargaJual = 0;
                    $diskonPersen = 0;
                    $discountStatus = 'No';

                    // Cari satuan ID dari formatted item
                    foreach ($result['satuans'] as $id => $satuan) {
                        if (str_contains($formattedItem, $satuan['name'])) {
                            $satuanId = $id;
                            $satuanName = $satuan['name'];
                            // Ambil harga dari result['prices'] jika ada
                            if (isset($result['prices'][$satuanId])) {
                                $hargaBeli = $result['prices'][$satuanId]['harga_beli'];
                                $hargaJual = $result['prices'][$satuanId]['harga_jual'];
                                $diskonPersen = $result['prices'][$satuanId]['diskon_persen'];
                                $discountStatus = $result['prices'][$satuanId]['diskon_status'];
                            } else {
                                // Fallback ke harga dasar jika tidak ada konversi harga
                                $hargaBeli = $barangKi->price_buy;
                                $hargaJual = $barangKi->price_sell;
                                $diskonPersen = 0;
                                $discountStatus = 'No';
                            }

                            break;
                        }
                    }

                    if ($satuanId !== null && isset($result['satuans'][$satuanId])) {
                        $satuan = $result['satuans'][$satuanId];

                        // Format harga dengan pemisah ribuan
                        $formattedHargaBeli = number_format($hargaBeli, 0, ',', '.');
                        $formattedHargaJual = number_format($hargaJual, 0, ',', '.');

                        // Tambahkan tombol dengan tooltip
                        $tooltip = "Harga Jual: Rp {$formattedHargaJual} | Harga Beli: Rp {$formattedHargaBeli}";
                        if ($discountStatus !== 'No' && $diskonPersen) {
                            $tooltip .= " | Diskon: {$discountStatus}({$diskonPersen})";
                        }

                        $buttons[] = "
                      <button 
                          type='button'  
                          class=' inline-flex items-center justify-center px-2 py-1 rounded-sm text-xs font-medium mr-1 mb-1 {$colorClass}'
                          tooltip title='{$tooltip}'
                      >
                          {$formattedItem}
                      </button>
                  ";

                        $index++;
                    }
                }

                return '<div class="flex flex-wrap">' . implode('', $buttons) . '</div>';
            })
            ->addColumn('price_buy', function ($barangKi) {
                return 'Rp' . number_format($barangKi->price_buy, 0, ',', '.');
            })
            ->addColumn('pricesell', function ($barangKi) {
                $hargaJual = $barangKi->price_sell;
                $discountStart = Carbon::parse($barangKi->discount_start);
                $discountEnd = Carbon::parse($barangKi->discount_end);
                $diskonPersen = null;

                if ($barangKi->discount_amount || $barangKi->discount_percentage) {
                    if ($discountStart <= Carbon::now() && Carbon::now() <= $discountEnd) {
                        if ($barangKi->discount_amount) {
                            $hargaNormal = $barangKi->price_sell;
                            $hargaJual = $barangKi->discount_amount;
                            $diskonPersen = round(($hargaNormal - $hargaJual) / $hargaNormal * 100) . '%';
                        } elseif ($barangKi->discount_percentage) {
                            $pricePersen = $hargaJual * ($barangKi->discount_percentage / 100);
                            $diskonPersen = round($barangKi->discount_percentage) . '%';
                            $hargaJual = $hargaJual - $pricePersen;
                        }
                        $message = "Ongoing";
                    } elseif ($discountStart >= Carbon::now()) {
                        if ($barangKi->discount_amount) {
                            $hargaNormal = $barangKi->price_sell;
                            $hargaDiskon = $barangKi->discount_amount;
                            $diskonPersen = round(($hargaNormal - $hargaDiskon) / $hargaNormal * 100) . '%';
                        } elseif ($barangKi->discount_percentage) {
                            $pricePersen = $hargaJual * ($barangKi->discount_percentage / 100);
                            $diskonPersen = round($barangKi->discount_percentage) . '%';
                        }
                        $message = "Coming";
                    } else {
                        $message = "No";
                    }
                } else {
                    $message = "No";
                }

                $hargaFormated = 'Rp' . number_format($hargaJual, 0, ',', '.');
                $classPersen = $message === 'Ongoing' ? 'inline-flex' : 'hidden';

                $html = '<span class="relative inline-flex items-center justify-center">
                ' . $hargaFormated . '
                    <span class="' . $classPersen . ' items-center gap-x-1 rounded-full bg-red-100 px-1.5 py-1 text-xs font-medium text-red-800 ">
                        <svg class="size-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 17 13.5 8.5 8.5 13.5 2 7" />
                        <polyline points="16 17 22 17 22 11" />
                        </svg>
                        ' . $diskonPersen . '
                    </span>
                </span>';
                return $html;
            })
            ->addColumn('barcode', function ($barangKi) {
                $html = '<span class="me-2 inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-gray-800 hover:text-blue-600">
                <i class="fad fa-barcode mr-1"></i>' . $barangKi->id_barcode . '
                </span>';
                return $html;
            })

            ->filterColumn('barcode', function ($query, $keyword) {
                $query->whereIn('barang_id', function ($sub) use ($keyword) {
                    $sub->select('barang_id')
                        ->from('barang_ki')
                        ->where('id_barcode', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('barang', function ($barangKi) {
                $image = $barangKi->barang->images && $barangKi->barang->images->isNotEmpty() ? '<img src="' . url('storage/' . $barangKi->barang->images->first()->url) . '" alt="' . $barangKi->barang->name . '" class="h-10 w-10 rounded-sm object-cover mr-2">' : '<div class="h-10 w-10 mr-2 rounded-sm bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-500">Image</div>';
                $html = '<div class="flex items-center whitespace-nowrap">
                        ' . $image . '
                        ' . $barangKi->barang->name . '
                     </div>';
                return $html;
            })
            ->addColumn('sold_item', function ($barangKi) {
                return $barangKi->where('barang_id', $barangKi->barang_id)->sum('sold_quantity');
            })

            ->addColumn('discount', function ($barangKi) {
                $diskon = $this->barangKIService->applyDiscountsToBarang($barangKi);
                $class = '';
                $classHidden = '';
                if ($diskon['discount_status'] === 'active') {
                    $class = 'bg-green-500';
                    $classHidden = '';
                } elseif ($diskon['discount_status'] === 'coming') {
                    $class = 'bg-yellow-500';
                    $classHidden = '';
                } else {
                    $class = 'bg-red-700';
                    $classHidden = 'hidden';
                }

                // Format persentase dengan menghilangkan trailing zeros
                $diskonPercentage = rtrim(rtrim(number_format($diskon['discount_percentage'], 2, '.', ''), '0'), '.');

                $html = '<button data-modal-target="timeline-modal' . $barangKi->id . '" data-modal-toggle="timeline-modal' . $barangKi->id . '" type="button" class="rounded px-2 py-0.5 text-xs font-medium text-white hover:opacity-70 capitalize focus:outline-none ' . $class . '">' . $diskon['discount_status'] . '<span class="' . $classHidden . '"> (' . $diskonPercentage . '%)</span>' .  '</button>';
                return $html;
            })
            ->addColumn('expired', function ($barangKi) {
                $html = $this->barangKIService->getExpiryDate($barangKi->expired_time, $barangKi->barang->early_expiry_days, $barangKi->barang->mid_expiry_days, $barangKi->barang->late_expiry_days);
                return $html;
            })
            ->addColumn('action', function ($barangKi) {
                $button = '<div class="inline-flex items-center space-x-1">';
                if (!$barangKi->deleted_at) {
                    if (auth()->user()->can('view.barang.ki')) {
                        $button .= '<button tooltip title="View" data-table="' . self::TABLE_ID . '" data-id="' . $barangKi->id_barcode . '" type="button" class="view-barang-button rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-eye"></i></span></button>';
                    }
                    if (auth()->user()->can('edit.barang.ki')) {
                        $button .= '<button tooltip title="Edit" data-table="' . self::TABLE_ID . '" data-id="' . $barangKi->id_barcode . '" type="button" class="edit-barang-button rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                    }
                    if (auth()->user()->can('delete.barang.ki')) {
                        if ($barangKi->barangtoko->count() === 0) {
                            $button .= '<button tooltip title="Delete" data-table="' . self::TABLE_ID . '" data-id="' . $barangKi->id_barcode . '" type="button" class="delete-barang-button rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                            $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                        }
                    }
                } else {
                    if (auth()->user()->can('delete.barang.ki')) {
                        $button .= '<button tooltip title="Restore" data-table="' . self::TABLE_ID . '"  data-id="' . $barangKi->id_barcode . '" type="button" class="restore-barang-button rounded bg-green-100 text-green-600 hover:text-green-600 hover:bg-green-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-trash-restore"></i></span></button>';
                    } else {
                        $button .= '<div tooltip title="has been deleted" data-table="' . self::TABLE_ID . '"  data-id="' . $barangKi->id_barcode . '" class="rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                        $button .=  '<span><i class="fad fa-times"></i></span></div>';
                    }
                }
                $button .= '</div> ';
                return $button;
            })
            ->filterColumn('barcode', function ($query, $keyword) {
                $query->where('id_barcode', 'like', "%" . $keyword . "%");
            })
            ->filterColumn('barang', function ($query, $keyword) {
                $query->whereHas('barang', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->rawColumns(['barcode', 'barang', 'quantity_item', 'pricesell', 'discount', 'expired', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BarangKI $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->selectRaw('
                MIN(id_barcode) as id_barcode,
                barang_id,
                expired_time,
                SUM(quantity) as quantity,
                SUM(sold_quantity) as sold_quantity,
                MAX(price_sell) as price_sell,
                MIN(discount_start) as discount_start,
                MIN(discount_end) as discount_end,
                MAX(discount_amount) as discount_amount,
                MAX(discount_percentage) as discount_percentage,
                CASE
                    WHEN MIN(discount_start) <= NOW() AND MIN(discount_end) >= NOW() THEN 1
                    WHEN MIN(discount_start) > NOW() THEN 2
                    ELSE 3
                END as discount_status,
                CASE
                    WHEN MAX(discount_amount) IS NOT NULL AND MAX(discount_amount) < MAX(price_sell) THEN MAX(discount_amount)
                    ELSE MAX(price_sell)
                END as discount_amount_price,
                CASE
                    WHEN MAX(discount_percentage) IS NOT NULL AND MAX(discount_percentage) > 0 THEN MAX(price_sell) - (MAX(price_sell) * MAX(discount_percentage) / 100)
                    ELSE MAX(price_sell)
                END as discount_percent_price,
                LEAST(
                    CASE WHEN MAX(discount_amount) IS NOT NULL AND MAX(discount_amount) < MAX(price_sell) THEN MAX(discount_amount) ELSE MAX(price_sell) END,
                    CASE WHEN MAX(discount_percentage) IS NOT NULL AND MAX(discount_percentage) > 0 THEN MAX(price_sell) - (MAX(price_sell) * MAX(discount_percentage) / 100) ELSE MAX(price_sell) END
                ) as best_discount_price,
                GREATEST(
                    CASE WHEN MAX(discount_amount) IS NOT NULL AND MAX(discount_amount) < MAX(price_sell) THEN ROUND((MAX(price_sell) - MAX(discount_amount))/MAX(price_sell)*100, 2) ELSE 0 END,
                    CASE WHEN MAX(discount_percentage) IS NOT NULL AND MAX(discount_percentage) > 0 THEN MAX(discount_percentage) ELSE 0 END
                ) as best_discount_percent
            ')
            ->with(['barang', 'barang.images', 'barang.subcategory', 'satuan'])
            ->groupBy('barang_id', 'expired_time')

            // Filter berdasarkan status diskon
            ->when($this->filters['discount'] ?? null, function ($query, $discount) {
                if ($discount === 'ongoing') {
                    $query->where(function ($query) {
                        $query->where('discount_start', '<=', now())
                            ->where('discount_end', '>=', now());
                    });
                } elseif ($discount === 'coming') {
                    $query->where(function ($query) {
                        $query->where('discount_start', '>', now());
                    });
                } elseif ($discount === 'expired_discount') {
                    $query->where(function ($query) {
                        $query->where('discount_end', '<', now())
                            ->whereNotNull('discount_start');
                    });
                } elseif ($discount === 'no_discount') {
                    $query->where(function ($query) {
                        $query->whereNull('discount_amount')
                            ->whereNull('discount_percentage');
                    });
                }
            })

            // Filter berdasarkan status barang (expired/active)
            ->when($this->filters['status'] ?? null, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('expired_time', '>', Carbon::now());
                } elseif ($status === 'deleted') {
                    $query->where('deleted_at', '!=', null);
                } elseif ($status === 'active') {
                    $query->where('status', 'active');
                } elseif ($status === 'nonactive') {
                    $query->where('status', 'nonactive');
                }
            })
            ->when($this->filters['expired'] ?? null, function ($query, $expired) {
                if ($expired === 'no_expiry') {
                    $query->whereNull('expired_time');
                } elseif ($expired === 'fresh') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.early_expiry_days DAY)');
                    });
                } elseif ($expired === 'early_expiry') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.early_expiry_days DAY)')
                            ->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)');
                    });
                } elseif ($expired === 'mid_expiry') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)')
                            ->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.late_expiry_days DAY)');
                    });
                } elseif ($expired === 'late_expiry') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.late_expiry_days DAY)')
                            ->whereRaw('DATE(expired_time) > DATE(NOW())');
                    });
                } elseif ($expired === 'expired') {
                    $query->whereRaw('DATE(expired_time) < DATE(NOW())');
                }
            })

            // Filter berdasarkan ketersediaan stok
            ->when($this->filters['stock'] ?? null, function ($query, $stock) {
                if ($stock === 'available') {
                    $query->havingRaw('SUM(quantity) > SUM(sold_quantity)');
                } elseif ($stock === 'out_of_stock') {
                    $query->havingRaw('SUM(quantity) <= SUM(sold_quantity)');
                } elseif ($stock === 'low_stock') {
                    // Misalnya low stock adalah ketika sisa stok <= 10
                    $query->havingRaw('(SUM(quantity) - SUM(sold_quantity)) <= 10 AND (SUM(quantity) - SUM(sold_quantity)) > 0');
                }
            })

            // Filter berdasarkan subcategory
            ->when($this->filters['subcategory'] ?? null, function ($query, $subcategory) {
                $query->whereHas('barang', function ($q) use ($subcategory) {
                    $q->where('subcategory_id', $subcategory);
                });
            })

            // Filter berdasarkan range harga
            ->when($this->filters['price_min'] ?? null, function ($query, $priceMin) {
                $query->havingRaw('MAX(price_sell) >= ?', [$priceMin]);
            })
            ->when($this->filters['price_max'] ?? null, function ($query, $priceMax) {
                $query->havingRaw('MAX(price_sell) <= ?', [$priceMax]);
            })

            ->orderBy('discount_status', 'asc')
            ->orderBy('best_discount_percent', 'desc')
            ->orderBy('discount_start', 'asc')
            ->orderBy('barang_id', 'asc')
            ->orderBy('expired_time', 'asc')
            ->orderBy('price_sell', 'desc');

        return $query;
    }
    /**
     * Optional method if you want to use the html builder.
     */
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
                'buttons' => [
                    $this->buttonExportShowing(self::TABLE_ID),
                    $this->customButton(self::TABLE_ID, ['excel', 'pdf', 'import'], 'Export & Import', 'fas fa-file-excel'),
                    $this->getColvisButton(),
                    $this->getReloadButton(self::TABLE_ID),
                ],
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'initComplete' => $this->getInitCompleteCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Cari Barang..',
                ],
            ]);
    }



    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('barcode')
                ->title('ID Produk')
                ->width('200px')
                ->className('dt-center whitespace-nowrap'),
            Column::make('barang')
                ->title('Barang')
                ->className('dt-center whitespace-nowrap not-export '),
            Column::make('quantity_item')
                ->title('Stock')
                ->searchable(false)
                ->orderable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900 '),
            Column::make('discount')
                ->title('Diskon')
                ->searchable(false)
                ->orderable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('expired')
                ->title('Expired')
                ->searchable(true)
                ->orderable(true)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::computed('action')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->title('ACTION')
                ->className('dt-center whitespace-nowrap font-medium text-gray-900 not-export not-colvis'),
        ];
    }

    /**
     * Get the editor fields definition.
     */
    public function editor(): Editor
    {
        return Editor::make()
            ->fields([
                Text::make('id_barcode')
                    ->label('Barcode ID'),
                Number::make('barang_id')
                    ->label('Barang ID'),
                Number::make('quantity')
                    ->label('quantity'),
                Number::make('price_buy')
                    ->label('Price Buy'),
                Number::make('price_sell')
                    ->label('Price Sell'),
                Number::make('discount_amount')
                    ->label('Discount Amount'),
                Number::make('discount_percentage')
                    ->label('Discount Percentage'),
                Date::make('discount_start')
                    ->label('Discount Start Date'),
                Date::make('discount_end')
                    ->label('Discount End Date'),
                Date::make('expired_time')
                    ->label('Expiry Date'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'nonactive' => 'Non-active',
                        'waiting' => 'Waiting'
                    ])
                    ->label('Status'),
            ]);
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'BarangKI_' . date('YmdHis');
    }
}
