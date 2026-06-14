<?php

namespace App\DataTables;

use App\Helpers\DateHelper;
use App\Models\Auth\UserModel;
use App\Traits\CustomDataTablePagination;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    use CustomDataTablePagination; // Gunakan trait
    protected $filterData;
    private const TABLE_ID = 'user-table';

    public function setFilter($filter)
    {
        $this->filterData = $filter;
        return $this;
    }
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
                $imageUrl = $row->profile_photo_path
                    ? url('storage/' . $row->profile_photo_path)
                    : null;

                $name = $row->name;
                $icon = '<i class="fad fa-shield-check mr-1"></i>';
                $twoFactor = $row->two_factor_enabled ? '<span class="space-x-1 text-xs px-2 py-1  text-green-700">' .

                    $icon . ' </span>' : '<span class="space-x-1 text-xs px-2 py-1 text-red-700">' . $icon . ' </span>';
                if ($row && isset($row->profile_photo_path) && $row->profile_photo_path) {
                    $ownerImage = '<img src="' . $imageUrl . '" alt="' . $row->name . '" mr-1 class="w-8 h-8 object-cover">';
                } else {
                    $ownerImage = '<div class="' . $row->getAvatarGradientClass() . ' mr-1 text-sm w-8 h-8 flex items-center justify-center rounded-sm bg-white/20 font-bold text-white">
                    ' . $row->initials . '
                </div>';
                }

                return '<div class="flex items-center">
                    ' . $ownerImage . '
                    <div class="flex flex-col ml-1">
                        <div class="font-medium flex items-center">
                            ' . $name . '
                            ' . $twoFactor . '
                        </div>
                        <div class="text-xs text-gray-500 ">@' . $row->username . '</div>
                    </div>
                </div>';
            })

            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';

                if (!$row->trashed()) {
                    $button .= '<button data-table="' . self::TABLE_ID . '" data-username="' . $row->username . '" class="edit-user-modal rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-pen"></i></span></button>';
                    $button .= '<button data-table="' . self::TABLE_ID . '" type="button" onclick="window.location.href = \'' . route('user.detail', $row->username) . '\'" class="detail-user-modal rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-eye"></i></span></button>';
                    $button .= '<button data-table="' . self::TABLE_ID . '" data-username="' . $row->username . '" class="delete-user-modal rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fad fa-trash"></i></span></button>';
                } else {
                    $button .= '<button data-table="' . self::TABLE_ID . '" data-username="' . $row->username . '" class="restore-user-modal rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                    $button .=  '<span><i class="fas fa-rotate"></i></span></button>';
                }
                $button .= '</div>';
                return $button;
            })

            ->editColumn('email_phone', function ($row) {
                $icon = '<i class="fad text-xs fa-envelope mr-1"></i>';
                $verfIcon = null;
                if ($row->email_verified_at) {
                    $verfIcon = '<i class="fad text-xs fa-check-circle ml-1 text-green-600"></i>';
                    $bgColor = '';
                    $textColor = '';
                } else {
                    $verfIcon = '<i class="fad text-xs fa-times-circle ml-1 text-red-600"></i>';
                    $bgColor = '';
                    $textColor = '';
                }
                $phoneIcon = '<i class="fad text-xs fa-phone mr-1"></i>';
                $phoneVerfIcon = null;
                if ($row->phone_verified_at) {
                    $phoneVerfIcon = '<i class="fad text-xs fa-check-circle ml-1 text-green-600"></i>';
                    $phoneBgColor = '';
                    $phoneTextColor = '';
                } else {
                    $phoneVerfIcon = '<i class="fad text-xs fa-times-circle ml-1 text-red-600"></i>';
                    $phoneBgColor = '';
                    $phoneTextColor = '';
                }
                return '<div class="space-y-1 flex flex-col">' .
                    ($row->email ? '<div><span class="space-x-1 text-xs rounded px-2 py-1 ' . $bgColor . ' ' . $textColor . '">' . $icon . ' ' . $row->email . '</span>' . $verfIcon . '</div>' : '') .
                    ($row->phone_number ? '<div><span class="space-x-1 text-xs rounded px-2 py-1 ' . $phoneBgColor . ' ' . $phoneTextColor . '">' . $phoneIcon . ' ' . $row->phone_number . '</span>' . $phoneVerfIcon . '</div>' : '') .
                    '</div>';
            })

            ->editColumn('dibuat', function ($row) {
                if ($row->deleted_at) {
                    return '<span class="text-red-600">(Deleted)</span>';
                } else {
                    return DateHelper::formatCreatedAt($row->created_at);
                }
            })

            ->editColumn('role', function ($row) {
                $rolesHtml = '';

                foreach ($row->roles as $role) {
                    $icon = '<i class="fad fa-user mr-1"></i>';
                    $bgColor = 'bg-gray-100';
                    $textColor = 'text-gray-700';

                    switch ($role->name) {
                        case 'founder':
                            $icon = '<i class="fad fa-crown mr-1"></i>';
                            $bgColor = 'bg-purple-100';
                            $textColor = 'text-purple-700';
                            break;
                        case 'programmer':
                            $icon = '<i class="fad fa-code mr-1"></i>';
                            $bgColor = 'bg-blue-100';
                            $textColor = 'text-blue-700';
                            break;
                        case 'admin':
                            $icon = '<i class="fad fa-user-shield mr-1"></i>';
                            $bgColor = 'bg-red-100';
                            $textColor = 'text-red-700';
                            break;
                        case 'accounting':
                            $icon = '<i class="fad fa-calculator mr-1"></i>';
                            $bgColor = 'bg-yellow-100';
                            $textColor = 'text-yellow-700';
                            break;
                        case 'operator':
                            $icon = '<i class="fad fa-tools mr-1"></i>';
                            $bgColor = 'bg-green-100';
                            $textColor = 'text-green-700';
                            break;
                        case 'guest':
                            $icon = '<i class="fad fa-user mr-1"></i>';
                            $bgColor = 'bg-gray-100';
                            $textColor = 'text-gray-700';
                            break;
                        case 'shop':
                            $icon = '<i class="fad fa-store mr-1"></i>';
                            $bgColor = 'bg-orange-100';
                            $textColor = 'text-orange-700';
                            break;
                    }

                    $permissions = $role->permissions->count();
                    $permissionsText = $permissions > 0 ? "<span class='text-xs text-gray-500'> (#{$permissions})</span>" : '';

                    $rolesHtml .= '<span class="space-x-1 text-sm rounded px-2 py-1 ' . $bgColor . ' ' . $textColor . ' mr-2">' . $icon . ' ' . $role->name . $permissionsText . '</span>';
                }

                return $rolesHtml;
            })

            ->editColumn('statusBadge', function ($row) {
                if ($row->deleted_at) {
                    return '<span class="text-red-600 bg-red-100 px-2 py-1 rounded">Deleted</span>';
                }
                if ($row->status == 'active') {
                    return '<span class="text-green-600 bg-green-100 px-2 py-1 rounded">Active</span>';
                } elseif ($row->status == 'suspended') {
                    return '<span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Suspended</span>';
                } else {
                    return '<span class="text-gray-600 bg-gray-100 px-2 py-1 rounded">Inactive</span>';
                }
            })

            ->filterColumn('name', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('name', 'like', "%" . $keyword . "%");
            })
            ->filterColumn('email', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('email', 'like', "%" . $keyword . "%");
            })
            ->filterColumn('phone_number', function ($query, $keyword) {
                // Pencarian hanya berdasarkan 'name', bukan gambar atau kolom lain
                $query->where('phone_number', 'like', "%" . $keyword . "%");
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->where('created_at', 'like', "%" . $keyword . "%");
            })

            ->filterColumn('role', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%" . $keyword . "%");
                });
            })

            ->rawColumns(['action', 'data', 'email_phone', 'dibuat', 'role', 'statusBadge']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(UserModel $model): EloquentBuilder
    {
        return $model->withTrashed()->newQuery()
            ->with('roles')
            ->when(request()->has('filter') && request('filter') == ['status' => 'active'], function ($query) {
                $query->where('status', 'active');
                $query->whereNull('deleted_at');
            })
            ->when(request()->has('filter') && request('filter') == ['status' => 'suspended'], function ($query) {
                $query->where('status', 'suspended');
                $query->whereNull('deleted_at');
            })
            ->when(request()->has('filter') && request('filter') == ['status' => 'inactive'], function ($query) {
                $query->where('status', 'inactive');
                $query->whereNull('deleted_at');
            })
            ->when(request()->has('filter') && request('filter') == ['status' => 'deleted'], function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'founder'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'founder');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'programmer'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'programmer');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'admin'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'accounting'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'accounting');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'operator'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'operator');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'shop'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'shop');
                });
            })
            ->when(request()->has('filter') && request('filter') == ['role' => 'guest'], function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'guest');
                });
            })
            ->select([
                'users.*',
            ]);
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
            Column::make('data')
                ->title('Nama')
                ->orderable(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('name')
                ->title('Nama')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('username')
                ->title('Username')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('email')
                ->title('Email')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('phone_number')
                ->title('No. Telp')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-left whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('email_phone')
                ->title('Email & No. Telp')
                ->orderable(false)
                ->searchable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('role')
                ->title('Role')
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 '),
            Column::make('status')
                ->title('Status')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
            Column::make('statusBadge')
                ->title('Status')
                ->searchable(false)
                ->orderable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900'),
            Column::make('dibuat')
                ->title('Dibuat')
                ->orderable(false)
                ->searchable(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-export'),
            Column::make('created_at')
                ->title('Dibuat')
                ->orderable(false)
                ->visible(false)
                ->addClass('dt-center whitespace-nowrap font-medium text-gray-900 not-colvis'),
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
        return 'User_' . date('YmdHis');
    }
}
