<?php

namespace App\DataTables;

use App\Models\Toko\BarangToko;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\ConvertSatuanService;
use App\Traits\CustomDataTablePagination;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BarangTokoDataTable extends DataTable
{
    protected $filters;
    protected $convertService;
    protected $barangKIService;
    use CustomDataTablePagination;
    private const TABLE_ID = 'barangtoko-table';

    public function __construct(ConvertSatuanService $convertService, BarangKIService $barangKIService)
    {
        $this->barangKIService = $barangKIService;
        $this->convertService = $convertService;
    }

    public function setFilter($filter)
    {
        $this->filters = $filter;
        return $this;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('select', function ($row) {
                return '<input type="checkbox" name="barangtoko_checkbox[]" class="barangtoko_checkbox rounded" value="' . $row->id . '">';
            })
            ->addColumn('toko', function ($barangToko) {
                $imageUrl = $barangToko->toko->image
                    ? $barangToko->toko->image
                    : null;

                $name = $barangToko->toko->name;
                $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');

                if (!$imageUrl) {
                    return '<div class="flex items-center whitespace-nowrap">
                    <div class="h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800 text-sm">
                        ' . $initials . '
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">' . $name . '</p>
                        <p class="text-xs text-gray-500">' . ($barangToko->toko->owner->name ?? 'N/A') . '</p>
                    </div>
                </div>';
                }

                return '<div class="flex items-center whitespace-nowrap">
                <img src="' . $imageUrl . '" alt="Image" class="mr-3 h-10 w-10 rounded-full object-cover">
                <div class="ml-3">
                    <p class="font-medium text-gray-900">' . $name . '</p>
                    <p class="text-xs text-gray-500">(' . ($barangToko->toko->owner->name ?? 'N/A') . ')</p>
                </div>
            </div>';
            })
            ->addColumn('expiredTime', function ($barangToko) {
                $html = $this->barangKIService->getExpiryDate($barangToko->barangKi->expired_time, $barangToko->barangKi->barang->early_expiry_days, $barangToko->barangKi->barang->mid_expiry_days, $barangToko->barangKi->barang->late_expiry_days);
                return $html;
            })
            ->addColumn('category', function ($barangToko) {
                return $barangToko->barangKI->barang->subcategory->category ? $barangToko->barangKI->barang->subcategory->category->name : 'No Category';
            })
            ->addColumn('satuan', function ($barangToko) {
                $result = $this->convertService->convertStock($barangToko->barangKI, $barangToko->quantity);
                if (is_string($result)) {
                    return '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">'
                        . $result . '</span>';
                }

                if (!isset($result['formatted']) || empty($result['formatted'])) {
                    return '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-gray-100 text-gray-800">0</span>';
                }

                $buttons = [];
                $index = 0;
                $satuanId = null;

                foreach ($result['formatted'] as $formattedItem) {
                    foreach ($result['satuans'] as $id => $satuan) {
                        if (str_contains($formattedItem, $satuan['name'])) {
                            $satuanId = $id;
                            break;
                        }
                    }
                    $colorClass = 'bg-gray-100 text-gray-800';
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

                    if ($satuanId !== null && isset($result['satuans'][$satuanId])) {
                        $satuan = $result['satuans'][$satuanId];

                        $buttons[] = "
                      <button 
                          type='button'  
                          class=' inline-flex items-center justify-center px-2 py-1 rounded-sm text-xs font-medium mr-1 mb-1 {$colorClass}'
                          tooltip title='Terjual: {$barangToko->sold} {$barangToko->barangKI->satuan->name}'
                      >
                          {$formattedItem}
                      </button>
                  ";
                        $index++;
                    }
                }

                return '<div class="flex flex-wrap">' . implode('', $buttons) . '</div>';
            })
            ->addColumn('barang', function ($barangToko) {
                $barang = $barangToko->barangKI->barang;
                $name = $barang->name;
                return '<div class="flex items-center whitespace-nowrap">
                    <div>
                        <p class="font-medium text-gray-900">' . $name . '</p>
                        <p class="text-xs text-gray-500">' . ($barangToko->barangKI->id_barcode ?? 'N/A') . '</p>
                    </div>
                </div>';
            })
            ->addColumn('hargaJual', function ($barangToko) {
                $hargaJual = $barangToko->price_sell;
                $pricePersen = $barangToko->price_percentage;
                if ($pricePersen) {
                    $ditambahPersen = $hargaJual * ($pricePersen / 100);
                    $hargaJual = $hargaJual + $ditambahPersen;
                    $classPersen = 'inline-flex';
                } else {
                    $hargaJual = $hargaJual;
                    $classPersen = 'hidden';
                }
                $hargaFormated = 'Rp' . number_format($hargaJual, 0, ',', '.');
                $percenFormated = '+' . number_format($pricePersen, 0, ',', '.') . '%';
                $html = '<span class="relative inline-flex items-center justify-center">
                        ' . $hargaFormated . '&nbsp;
                             <span class="' . $classPersen . ' items-center gap-x-1 rounded-full bg-green-100 px-1.5 py-1 text-xs font-medium text-green-800 ">
                               ' . $percenFormated . '
                            </span>
                        </span>';
                return $html;
            })
            ->addColumn('action', function ($barang) {
                $button = '<div class="inline-flex items-center">';
                $button .= '<button data-id="edit-barang-toko-' . $barang->id . '" class="edit-barang-toko rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                $button .= '</div>';
                return $button;
            })
            ->filterColumn('toko', function ($query, $keyword) {
                $query->whereHas('toko', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('expiredTime', function ($query, $keyword) {
                $query->whereHas('barangKI', function ($q) use ($keyword) {
                    $q->where('expired_time', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('barangKI.barang.subcategory.category', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('satuan', function ($query, $keyword) {
                $query->whereHas('barangKI.satuan', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })
            ->filterColumn('barang', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('barangKI.barang', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%" . $keyword . "%");
                    })->orWhereHas('barangKI', function ($q2) use ($keyword) {
                        $q2->where('id_barcode', 'like', "%" . $keyword . "%");
                    });
                });
            })
            ->filterColumn('hargaJual', function ($query, $keyword) {
                $query->where('price_sell', 'like', "%" . $keyword . "%");
            })
            ->rawColumns(['select', 'toko', 'category', 'satuan', 'barang', 'hargaJual', 'expiredTime', 'action']);
    }

    public function query(BarangToko $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->selectRaw('
                barang_toko.id,
                barang_toko.barangki_id,
                barang_toko.toko_id,
                barang_toko.quantity,
                barang_toko.sold,
                barang_toko.price_sell,
                barang_toko.price_buy,
                barang_toko.price_percentage,
                barang_toko.created_at,
                barang_toko.updated_at,
                barang_ki.expired_time,
                barang_ki.id_barcode,
                barang_ki.price_sell as barang_ki_price_sell,
                barang_ki.discount_amount,
                barang_ki.discount_percentage,
                barang_ki.discount_start,
                barang_ki.discount_end,
                barang.name as barang_name,
                barang.sku,
                barang.early_expiry_days,
                barang.mid_expiry_days,
                barang.late_expiry_days,
                brands.name as brand_name,
                type_barang.name as type_name,
                satuan_items.name as satuan_name,
                sub_categories.name as subcategory_name,
                categories.name as category_name,
                toko.name as toko_name,
                CASE 
                    WHEN barang_ki.expired_time IS NULL THEN "no_expiry"
                    WHEN DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.early_expiry_days DAY) THEN "fresh"
                    WHEN DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.early_expiry_days DAY) 
                         AND DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.mid_expiry_days DAY) THEN "early_expiry"
                    WHEN DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.mid_expiry_days DAY) 
                         AND DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.late_expiry_days DAY) THEN "mid_expiry"
                    WHEN DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.late_expiry_days DAY) 
                         AND DATE(barang_ki.expired_time) > DATE(NOW()) THEN "late_expiry"
                    WHEN DATE(barang_ki.expired_time) < DATE(NOW()) THEN "expired"
                    ELSE "unknown"
                END as expiry_status,
                CASE 
                    WHEN barang_ki.expired_time IS NULL THEN NULL
                    WHEN DATE(barang_ki.expired_time) < DATE(NOW()) THEN DATEDIFF(NOW(), barang_ki.expired_time)
                    ELSE DATEDIFF(barang_ki.expired_time, NOW())
                END as days_to_expiry
            ')
            ->leftJoin('barang_ki', 'barang_toko.barangki_id', '=', 'barang_ki.id')
            ->leftJoin('barang', 'barang_ki.barang_id', '=', 'barang.id')
            ->leftJoin('brands', 'barang.brand_id', '=', 'brands.id')
            ->leftJoin('type_barang', 'barang.type_id', '=', 'type_barang.id')
            ->leftJoin('satuan_items', 'barang_ki.satuan_id', '=', 'satuan_items.id')
            ->leftJoin('sub_categories', 'barang.subcategory_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->leftJoin('toko', 'barang_toko.toko_id', '=', 'toko.id');

        $this->applyFilters($query);
        $query->distinct();
        return $query;
    }

    private function applyFilters(QueryBuilder $query): void
    {
        // Filter by stock status
        $query->when($this->filters['stock_status'] ?? null, function ($query, $stockStatus) {
            switch ($stockStatus) {
                case 'available':
                    $query->where('barang_toko.quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('barang_toko.quantity', '>', 0)
                        ->where('barang_toko.quantity', '<=', 10);
                    break;
                case 'out_of_stock':
                    $query->where('barang_toko.quantity', '=', 0);
                    break;
                case 'has_sales':
                    $query->where('barang_toko.sold', '>', 0);
                    break;
                case 'no_sales':
                    $query->where('barang_toko.sold', '=', 0);
                    break;
            }
        });

        // Filter by expiry status
        $query->when($this->filters['expired'] ?? null, function ($query, $expired) {
            switch ($expired) {
                case 'no_expiry':
                    $query->whereNull('barang_ki.expired_time');
                    break;
                case 'early_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.early_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)');
                    break;
                case 'mid_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.late_expiry_days DAY)');
                    break;
                case 'late_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.late_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW())');
                    break;
                case 'expired':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW())');
                    break;
            }
        });

        // Filter by category
        $query->when($this->filters['category_id'] ?? null, function ($query, $categoryId) {
            $query->where('categories.id', $categoryId);
        });

        // Filter by subcategory
        $query->when($this->filters['subcategory_id'] ?? null, function ($query, $subcategoryId) {
            $query->where('sub_categories.id', $subcategoryId);
        });

        // Filter by brand
        $query->when($this->filters['brand_id'] ?? null, function ($query, $brandId) {
            $query->where('brands.id', $brandId);
        });

        // Filter by type
        $query->when($this->filters['type_id'] ?? null, function ($query, $typeId) {
            $query->where('type_barang.id', $typeId);
        });

        // Filter by toko
        $query->when($this->filters['toko_id'] ?? null, function ($query, $tokoId) {
            if (is_array($tokoId)) {
                $query->whereIn('barang_toko.toko_id', $tokoId);
            } else {
                $query->where('barang_toko.toko_id', $tokoId);
            }
        });

        // Filter by price range
        $query->when($this->filters['price_min'] ?? null, function ($query, $priceMin) {
            $query->where('barang_toko.price_sell', '>=', $priceMin);
        });

        $query->when($this->filters['price_max'] ?? null, function ($query, $priceMax) {
            $query->where('barang_toko.price_sell', '<=', $priceMax);
        });

        // Filter by date range
        $query->when($this->filters['created_from'] ?? null, function ($query, $createdFrom) {
            $query->whereDate('barang_toko.created_at', '>=', $createdFrom);
        });

        $query->when($this->filters['created_to'] ?? null, function ($query, $createdTo) {
            $query->whereDate('barang_toko.created_at', '<=', $createdTo);
        });

        // Filter by quantity range
        $query->when($this->filters['quantity_min'] ?? null, function ($query, $quantityMin) {
            $query->where('barang_toko.quantity', '>=', $quantityMin);
        });

        $query->when($this->filters['quantity_max'] ?? null, function ($query, $quantityMax) {
            $query->where('barang_toko.quantity', '<=', $quantityMax);
        });

        // Filter by sold quantity range
        $query->when($this->filters['sold_min'] ?? null, function ($query, $soldMin) {
            $query->where('barang_toko.sold', '>=', $soldMin);
        });

        $query->when($this->filters['sold_max'] ?? null, function ($query, $soldMax) {
            $query->where('barang_toko.sold', '<=', $soldMax);
        });

        // Filter by profit margin
        $query->when($this->filters['profit_margin_min'] ?? null, function ($query, $profitMarginMin) {
            $query->whereRaw('((barang_toko.price_sell - barang_toko.price_buy) / barang_toko.price_buy * 100) >= ?', [$profitMarginMin]);
        });

        $query->when($this->filters['profit_margin_max'] ?? null, function ($query, $profitMarginMax) {
            $query->whereRaw('((barang_toko.price_sell - barang_toko.price_buy) / barang_toko.price_buy * 100) <= ?', [$profitMarginMax]);
        });

        // Filter by status
        $query->when($this->filters['status'] ?? null, function ($query, $status) {
            $query->where('barang.status', $status);
        });

        // Default filter: only show items with stock or sales history
        if (!isset($this->filters['show_all'])) {
            $query->where(function ($q) {
                $q->where('barang_toko.quantity', '>', 0)
                    ->orWhere('barang_toko.sold', '>', 0);
            });
        }
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
                    $this->customButton(self::TABLE_ID, ['excel', 'pdf']),
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

    public function getColumns(): array
    {
        return [
            Column::make('select')
                ->title('<input type="checkbox" class="rounded" id="master">')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900 not-export not-colvis'),
            Column::make('toko')
                ->title('Toko')
                ->className('dt-left whitespace-nowrap font-medium text-gray-900'),
            Column::make('barang')
                ->title('Barang')
                ->className('dt-left whitespace-nowrap font-medium text-gray-900'),
            Column::make('category')
                ->title('Kategori')
                ->searchable(true)
                ->orderable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('satuan')
                ->title('Stock')
                ->searchable(false)
                ->orderable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('hargaJual')
                ->title('Harga Jual')
                ->searchable(false)
                ->orderable(false)
                ->className('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('expiredTime')
                ->title('Expired')
                ->searchable(false)
                ->orderable(false)
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
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'barangtoko_' . date('YmdHis');
    }
}
