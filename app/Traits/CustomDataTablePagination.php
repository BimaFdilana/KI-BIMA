<?php

namespace App\Traits;

trait CustomDataTablePagination
{
    /**
     * Generate custom pagination callback for DataTable
     * 
     * @param string $tableId - Unique table identifier
     * @return string - JavaScript callback function
     */
    protected function getCustomPaginationCallback($tableId)
    {
        return 'function() {
                    // PENTING: Gunakan table ID yang spesifik untuk menghindari collision
                    var tableId = "' . $tableId . '";
                    var api = this.api();
                    var info = api.page.info();
                    var pageTotal = info.pages;
                    
                    // Gunakan selector yang spesifik untuk table ini
                    var paginateContainer = $("#" + tableId + "_wrapper .dataTables_paginate");
                    
                    // Hapus pagination sebelumnya untuk table ini saja
                    paginateContainer.empty();
                    
                    // Buat container untuk custom pagination
                    var customPagination = $("<div>").addClass("custom-pagination-" + tableId + " flex items-center justify-end space-x-2");
                    
                    // Buat previous button dengan ID unik
                    var prevBtn = $("<button>")
                        .text("<")
                        .addClass("prev-btn-" + tableId + " bg-gray-50 cursor-pointer border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-1")
                        .attr("type", "button")
                        .attr("id", "prev-" + tableId);
                        
                    if (info.page === 0) {
                        prevBtn.addClass("opacity-50 cursor-not-allowed").prop("disabled", true);
                        prevBtn.removeClass("cursor-pointer");
                    }
                    
                    // Buat input halaman dengan ID unik - TANPA SPINNER
                    var pageInput = $("<input>")
                        .attr({
                            "type": "number",
                            "min": 1,
                            "max": pageTotal,
                            "value": info.page + 1,
                            "id": "page-input-" + tableId
                        })
                        .addClass("page-input-" + tableId + " bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 w-16 px-3 py-1 text-center")
                        .css({
                            "-moz-appearance": "textfield"  // Firefox: hilangkan spinner
                        });
                    
                    // Buat teks "of [total pages]"
                    var ofText = $("<span>").text("of " + pageTotal).addClass("text-sm text-gray-600");
                    
                    // Buat next button dengan ID unik
                    var nextBtn = $("<button>")
                        .text(">")
                        .addClass("next-btn-" + tableId + " bg-gray-50 border cursor-pointer border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-1")
                        .attr("type", "button")
                        .attr("id", "next-" + tableId);
                        
                    if (info.page >= info.pages - 1) {
                        nextBtn.addClass("opacity-50 cursor-not-allowed").prop("disabled", true);
                        nextBtn.removeClass("cursor-pointer");
                    }
                    
                    // Tambahkan semua elemen ke custom pagination
                    customPagination.append(prevBtn).append(pageInput).append(ofText).append(nextBtn);
                    
                    // Tambahkan ke container pagination
                    paginateContainer.append(customPagination);
                    
                    // PENTING: Hapus event listeners lama untuk menghindari multiple bindings
                    $(document).off("click", "#prev-" + tableId);
                    $(document).off("click", "#next-" + tableId);
                    $(document).off("keypress change blur", "#page-input-" + tableId);
                    
                    // Event listener untuk previous button (dengan delegation)
                    $(document).on("click", "#prev-" + tableId, function(e) {
                        e.preventDefault();
                        if (!$(this).prop("disabled")) {
                            api.page("previous").draw(false);
                        }
                    });
                    
                    // Event listener untuk next button (dengan delegation)
                    $(document).on("click", "#next-" + tableId, function(e) {
                        e.preventDefault();
                        if (!$(this).prop("disabled")) {
                            api.page("next").draw(false);
                        }
                    });
                    
                    // Event listener untuk input halaman (dengan delegation)
                    $(document).on("keypress", "#page-input-" + tableId, function(e) {
                        if (e.which === 13) { // Enter key
                            var page = parseInt($(this).val()) - 1;
                            if (page >= 0 && page < info.pages) {
                                api.page(page).draw(false);
                            } else {
                                $(this).val(info.page + 1); // Reset ke halaman current jika invalid
                            }
                        }
                    });
                    
                    // Event listener untuk blur input (auto-correct)
                    $(document).on("change blur", "#page-input-" + tableId, function() {
                        var page = parseInt($(this).val()) - 1;
                        if (page >= 0 && page < info.pages) {
                            api.page(page).draw(false);
                        } else {
                            $(this).val(info.page + 1); // Reset ke halaman current jika invalid
                        }
                    });
                }';
    }

    /**
     * Generate init complete callback for DataTable styling
     * 
     * @param string $tableId - Unique table identifier
     * @return string - JavaScript callback function
     */
    protected function getInitCompleteCallback($tableId)
    {
        return 'function() {
                    var tableId = "' . $tableId . '";
                    
                    // Style untuk header table
                    $("#" + tableId + " thead").addClass("bg-gray-50 text-xs uppercase text-gray-700");
                    $("#" + tableId + " th").removeClass("py-2 font-medium").addClass("py-4");
                    
                    // Style untuk buttons - gunakan selector spesifik
                    $("#" + tableId + "_wrapper .buttons-collection, #" + tableId + "_wrapper .dt-button").addClass("dt-button-custom");
                    $("#" + tableId + "_wrapper .dt-button, #" + tableId + "_wrapper .dt-button-custom").css("margin-right", "5px");
                    
                    // CSS untuk menghilangkan spinner pada input number pagination
                    if (!$("#pagination-number-input-style").length) {
                        var style = $("<style>").attr("id", "pagination-number-input-style").html(`
                            /* Chrome, Safari, Edge, Opera */
                            .page-input-' . $tableId . '::-webkit-outer-spin-button,
                            .page-input-' . $tableId . '::-webkit-inner-spin-button {
                                -webkit-appearance: none;
                                margin: 0;
                            }
                            
                            /* Firefox */
                            .page-input-' . $tableId . '[type=number] {
                                -moz-appearance: textfield;
                            }
                            
                            /* Additional styling untuk semua table pagination inputs */
                            input[id^="page-input-"]::-webkit-outer-spin-button,
                            input[id^="page-input-"]::-webkit-inner-spin-button {
                                -webkit-appearance: none;
                                margin: 0;
                            }
                            
                            input[id^="page-input-"][type=number] {
                                -moz-appearance: textfield;
                            }
                        `);
                        $("head").append(style);
                    }
                    
                }';
    }

    /**
     * Get standard buttons configuration for DataTable
     * 
     * @param string $tableId - Unique table identifier
     * @return array - Buttons configuration
     */
    protected function getStandardButtons($tableId)
    {
        return [
            [
                'text' => '<i class="fas fa-download"></i> Export (Showing Data)',
                'className' => 'bg-green-500 hover:bg-green-600 text-white export-btn',
                'extend' => 'collection',
                'autoClose' => true, // Auto close collection after click
                'buttons' => [
                    [
                        'extend' => 'excel',
                        'text' => '<i class="fas fa-file-excel mr-2"></i> Excel',
                        'className' => 'btn-sm btn-success',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'csv',
                        'text' => '<i class="fas fa-file-csv mr-2"></i> CSV',
                        'className' => 'btn-sm btn-success',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'pdf',
                        'text' => '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        'className' => 'btn-sm btn-danger',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'print',
                        'text' => '<i class="fas fa-print mr-2"></i> Print',
                        'className' => 'btn-sm btn-info',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                ],
            ],
            [
                'extend' => 'colvis',
                'text' => '<i class="fas fa-columns"></i> Kolom',
                'className' => 'btn-sm bg-yellow-500 hover:bg-yellow-600 text-white',
                'columns' => ':not(.not-colvis)',
                'autoClose' => true, // Auto close colvis after selection
            ],
            [
                'text' => '<i class="fas fa-sync"></i> Reload',
                'className' => 'btn-sm bg-yellow-500 hover:bg-yellow-600 text-white',
                'action' => 'function() { $("#' . $tableId . '").DataTable().ajax.reload(); }'
            ],
        ];
    }

    protected function buttonExportShowing($tableId)
    {
        return [
            [
                'text' => '<i class="fas fa-download"></i> Export (Showing Data)',
                'className' => 'bg-green-500 hover:bg-green-600 text-white export-btn',
                'extend' => 'collection',
                'autoClose' => true, // Auto close collection after click
                'buttons' => [
                    [
                        'extend' => 'excel',
                        'text' => '<i class="fas fa-file-excel mr-2"></i> Excel',
                        'className' => 'btn-sm btn-success',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'csv',
                        'text' => '<i class="fas fa-file-csv mr-2"></i> CSV',
                        'className' => 'btn-sm btn-success',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'pdf',
                        'text' => '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        'className' => 'btn-sm btn-danger',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                    [
                        'extend' => 'print',
                        'text' => '<i class="fas fa-print mr-2"></i> Print',
                        'className' => 'btn-sm btn-info',
                        'exportOptions' => [
                            'columns' => ':not(.not-export)',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function customButton($tableId, array $buttons = [], $label = 'Options', $icon = 'fas fa-cog')
    {
        // default buttons yang bisa dipakai
        $available = [
            'excel' => [
                'text' => '<i class="fas fa-file-excel mr-2"></i> Export Excel',
                'className' => 'btn-sm bg-green-500 hover:bg-green-600 text-white',
                'action' => 'function(e, dt, button, config) {
                    handleExport("' . $tableId . '", "excel");
                }'
            ],
            'pdf' => [
                'text' => '<i class="fas fa-file-pdf mr-2"></i> Export PDF',
                'className' => 'btn-sm bg-red-500 hover:bg-red-600 text-white',
                'action' => 'function(e, dt, button, config) {
                    handleExport("' . $tableId . '", "pdf");
                }'
            ],
            'csv' => [
                'text' => '<i class="fas fa-file-csv mr-2"></i> Export CSV',
                'className' => 'btn-sm bg-yellow-500 hover:bg-yellow-600 text-white',
                'action' => 'function(e, dt, button, config) {
                    handleExport("' . $tableId . '", "csv");
                }'
            ],
            'import' => [
                'text' => '<i class="fas fa-file-upload mr-2"></i> Import Data',
                'className' => 'btn-sm bg-blue-500 hover:bg-blue-600 text-white',
                'action' => 'function(e, dt, button, config) {
                    handleImport("' . $tableId . '");
                }'
            ],
            'print' => [
                'extend' => 'print',
                'text' => '<i class="fas fa-print mr-2"></i> Print',
                'className' => 'btn-sm bg-indigo-500 hover:bg-indigo-600 text-white',
                'exportOptions' => [
                    'columns' => ':not(.not-export)',
                ],
            ],
            'colvis' => [
                'extend' => 'colvis',
                'text' => '<i class="fas fa-columns mr-2"></i> Kolom',
                'className' => 'btn-sm bg-purple-500 hover:bg-purple-600 text-white',
                'columns' => ':not(.not-colvis)',
                'autoClose' => true, // Auto close colvis after selection
            ],
            'reload' => [
                'text' => '<i class="fas fa-sync mr-2"></i> Reload',
                'className' => 'btn-sm bg-gray-500 hover:bg-gray-600 text-white',
                'action' => 'function() {
                    $("#' . $tableId . '").DataTable().ajax.reload();
                }'
            ],
        ];

        $selected = [];

        // kalau parameter kosong → pakai semua default
        if (empty($buttons)) {
            $selected = array_values($available);
        } else {
            foreach ($buttons as $btn) {
                if (isset($available[$btn])) {
                    $selected[] = $available[$btn];
                }
            }
        }

        return [
            'text' => '<i class="' . $icon . '"></i> ' . $label,
            'className' => 'btn-sm bg-blue-600 hover:bg-blue-700 text-white',
            'extend' => 'collection',
            'autoClose' => true, // Auto close collection after click
            'buttons' => $selected
        ];
    }

    protected function getExportExcelButton($tableId)
    {
        return [
            'text' => '<i class="fas fa-file-excel mr-2"></i> Excel',
            'className' => 'btn-sm bg-green-500 hover:bg-green-600 text-white export-excel-btn',
            'action' => 'function(e, dt, button, config) {
                handleExportExcel("' . $tableId . '");
            }'
        ];
    }

    protected function getExportCsvButton($tableId)
    {
        return [
            'text' => '<i class="fas fa-file-csv mr-2"></i> CSV',
            'className' => 'btn-sm bg-yellow-500 hover:bg-yellow-600 text-white export-csv-btn',
            'action' => 'function(e, dt, button, config) {
                handleExportCSV("' . $tableId . '");
            }'
        ];
    }

    protected function getExportPdfButton($tableId)
    {
        return [
            'text' => '<i class="fas fa-file-pdf mr-2"></i> PDF',
            'className' => 'btn-sm bg-red-500 hover:bg-red-600 text-white export-pdf-btn',
            'action' => 'function(e, dt, button, config) {
                handleExportPDF("' . $tableId . '");
            }'
        ];
    }

    protected function getPrintButton($tableId)
    {
        return [
            'extend' => 'print',
            'text' => '<i class="fas fa-print mr-2"></i> Print',
            'className' => 'btn-sm bg-blue-500 hover:bg-blue-600 text-white',
            'exportOptions' => [
                'columns' => ':not(.not-export)',
            ],
        ];
    }

    protected function getReloadButton($tableId)
    {
        return [
            'text' => '<i class="fas fa-sync"></i> Reload',
            'className' => 'btn-sm bg-gray-500 hover:bg-gray-600 text-white',
            'action' => 'function() { $("#' . $tableId . '").DataTable().ajax.reload(); }'
        ];
    }

    protected function getColvisButton()
    {
        return [
            'extend' => 'colvis',
            'text' => '<i class="fas fa-columns"></i> Kolom',
            'className' => 'btn-sm bg-purple-500 hover:bg-purple-600 text-white',
            'columns' => ':not(.not-colvis)',
            'autoClose' => true, // Auto close colvis after selection
        ];
    }
}
