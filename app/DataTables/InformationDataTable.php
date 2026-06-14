<?php

namespace App\DataTables;

use App\Helpers\DateHelper;
use App\Models\Information\Information;
use App\Traits\CustomDataTablePagination;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InformationDataTable extends DataTable
{
    use CustomDataTablePagination;

    private const TABLE_ID = 'information-table';

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
            ->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '-';
            })
            ->addColumn('author', function ($row) {
                return $row->user ? $row->user->name : 'Unknown';
            })
            ->editColumn('is_published', function ($row) {
                if ($row->is_published) {
                    return '<span class="text-green-600 bg-green-100 px-2 py-1 rounded">Published</span>';
                } else {
                    return '<span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Draft</span>';
                }
            })
            ->editColumn('visibility', function ($row) {
                $color = $row->visibility === 'public' ? 'blue' : 'gray';
                return '<span class="text-' . $color . '-600 bg-' . $color . '-100 px-2 py-1 rounded capitalize">' . $row->visibility . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return DateHelper::formatCreatedAt($row->created_at);
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="inline-flex items-center space-x-1">';

                $button .= '<button data-table="' . self::TABLE_ID . '" type="button" onclick="window.location.href = \'' . route('information.edit', $row->id) . '\'" class="edit-information-modal rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-pen"></i></span></button>';

                $button .= '<button data-table="' . self::TABLE_ID . '" type="button" onclick="window.location.href = \'' . route('information.show', $row->id) . '\'" class="detail-information-modal rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-eye"></i></span></button>';

                $button .= '<button data-table="' . self::TABLE_ID . '" data-id="' . $row->id . '" class="delete-information-modal rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex space-x-1 items-center">';
                $button .=  '<span><i class="fad fa-trash"></i></span></button>';

                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action', 'is_published', 'visibility']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Information $model): EloquentBuilder
    {
        return $model->newQuery()->with(['category', 'user']);
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
                'buttons' => $this->getStandardButtons(self::TABLE_ID),
                'drawCallback' => $this->getCustomPaginationCallback(self::TABLE_ID),
                'language' => [
                    'info' => 'Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries',
                    'search' => '',
                    'searchPlaceholder' => 'Search Information...',
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
     * Get columns definition for dataTable.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->width(50)
                ->addClass('text-center align-middle'),
            Column::make('title')
                ->title('Title')
                ->addClass('align-middle font-medium text-gray-900'),
            Column::make('category_name')
                ->title('Category')
                ->addClass('align-middle text-gray-700'),
            Column::make('author')
                ->title('Author')
                ->addClass('align-middle text-gray-700'),
            Column::make('visibility')
                ->title('Visibility')
                ->addClass('text-center align-middle'),
            Column::make('is_published')
                ->title('Status')
                ->addClass('text-center align-middle'),
            Column::make('created_at')
                ->title('Created At')
                ->addClass('text-center align-middle'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Information_' . date('YmdHis');
    }
}
