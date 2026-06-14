/**
 * DataTable Scanner Manager
 * Handles barcode scanning for multiple DataTables dynamically
 */
class DataTableScannerManager {
  constructor() {
    this.scanBuffer = "";
    this.scanTimeout = null;
    this.scanTimeoutDuration = 100; // ms between characters for scan detection
    this.minScanLength = 8; // minimum length to consider as barcode
    this.activeTables = new Map(); // Store active DataTable instances
    this.currentFocusedTable = null;

    this.init();
  }

  init() {
    this.detectDataTables();
    this.bindEvents();
    this.setupTableFocusDetection();
  }

  /**
   * Automatically detect all DataTables on the page
   */
  detectDataTables() {
    // Detect DataTables by looking for tables with DataTable class
    $("table.dataTable").each((index, table) => {
      const tableId = $(table).attr("id");
      if (tableId && $.fn.DataTable.isDataTable(`#${tableId}`)) {
        const dataTable = $(`#${tableId}`).DataTable();
        this.activeTables.set(tableId, {
          instance: dataTable,
          searchInput: $(`#${tableId}_filter input`),
          container: $(table).closest(".dataTables_wrapper"),
        });

        console.log(`Scanner registered for table: ${tableId}`);
      }
    });

    // If no tables detected, try common table IDs
    const commonTableIds = ["barang-table-ki", "users-table", "products-table"];
    commonTableIds.forEach((tableId) => {
      if (
        $(`#${tableId}`).length &&
        $.fn.DataTable.isDataTable(`#${tableId}`)
      ) {
        const dataTable = $(`#${tableId}`).DataTable();
        this.activeTables.set(tableId, {
          instance: dataTable,
          searchInput: $(`#${tableId}_filter input`),
          container: $(`#${tableId}`).closest(".dataTables_wrapper"),
        });
      }
    });
  }

  /**
   * Setup focus detection for tables
   */
  setupTableFocusDetection() {
    this.activeTables.forEach((tableData, tableId) => {
      // Set focus when user clicks on table or search input
      tableData.container.on("click", () => {
        this.setActiveTable(tableId);
      });

      tableData.searchInput.on("focus", () => {
        this.setActiveTable(tableId);
      });

      // Visual indicator for active table
      tableData.container.on("mouseenter", () => {
        this.setActiveTable(tableId);
      });
    });

    // Set first table as default active
    if (this.activeTables.size > 0) {
      const firstTableId = this.activeTables.keys().next().value;
      this.setActiveTable(firstTableId);
    }
  }

  /**
   * Set active table for scanning
   */
  setActiveTable(tableId) {
    if (this.activeTables.has(tableId)) {
      this.currentFocusedTable = tableId;

      // Remove active indicator from all tables
      this.activeTables.forEach((tableData) => {
        tableData.container.removeClass("scanner-active");
      });

      // Add active indicator to current table
      this.activeTables.get(tableId).container.addClass("scanner-active");

      console.log(`Active scanner table: ${tableId}`);
    }
  }

  /**
   * Bind keyboard events for scan detection
   */
  bindEvents() {
    $(document).on("keydown", (e) => {
      // Ignore if user is typing in input fields (except DataTable search)
      const activeElement = document.activeElement;
      const isDataTableSearch =
        $(activeElement).closest(".dataTables_filter").length > 0;

      if (
        activeElement.tagName === "INPUT" ||
        activeElement.tagName === "TEXTAREA" ||
        activeElement.tagName === "SELECT"
      ) {
        if (!isDataTableSearch) {
          return;
        }
      }

      // Handle Enter key - trigger search if we have scan data
      if (e.keyCode === 13 && this.scanBuffer.length >= this.minScanLength) {
        e.preventDefault();
        this.processScan();
        return;
      }

      // Ignore special keys
      if (this.isSpecialKey(e.keyCode)) {
        return;
      }

      // Add character to scan buffer
      const char = String.fromCharCode(e.keyCode);
      if (char && char.match(/[a-zA-Z0-9]/)) {
        this.scanBuffer += char;
        this.resetScanTimeout();
      }
    });

    // Handle paste events (some scanners simulate paste)
    $(document).on("paste", (e) => {
      setTimeout(() => {
        const pastedData = (
          e.originalEvent.clipboardData || window.clipboardData
        ).getData("text");
        if (pastedData && pastedData.length >= this.minScanLength) {
          this.scanBuffer = pastedData;
          this.processScan();
        }
      }, 10);
    });
  }

  /**
   * Check if key is a special key that should be ignored
   */
  isSpecialKey(keyCode) {
    const specialKeys = [
      8, // backspace
      9, // tab
      16, // shift
      17, // ctrl
      18, // alt
      20, // caps lock
      27, // escape
      37, // left arrow
      38, // up arrow
      39, // right arrow
      40, // down arrow
      112, // F1
      113, // F2
      114, // F3
      115, // F4
      116, // F5
      117, // F6
      118, // F7
      119, // F8
      120, // F9
      121, // F10
      122, // F11
      123, // F12
    ];

    return specialKeys.includes(keyCode);
  }

  /**
   * Reset scan timeout
   */
  resetScanTimeout() {
    if (this.scanTimeout) {
      clearTimeout(this.scanTimeout);
    }

    this.scanTimeout = setTimeout(() => {
      if (this.scanBuffer.length >= this.minScanLength) {
        this.processScan();
      } else {
        this.clearScanBuffer();
      }
    }, this.scanTimeoutDuration);
  }

  /**
   * Process the scanned barcode
   */
  processScan() {
    if (
      !this.currentFocusedTable ||
      !this.activeTables.has(this.currentFocusedTable)
    ) {
      console.warn("No active table for scanning");
      this.clearScanBuffer();
      return;
    }

    const scannedCode = this.scanBuffer.trim();
    if (scannedCode.length < this.minScanLength) {
      this.clearScanBuffer();
      return;
    }

    console.log(
      `Scanned barcode: ${scannedCode} for table: ${this.currentFocusedTable}`
    );

    const tableData = this.activeTables.get(this.currentFocusedTable);

    // Clear previous search and apply new search
    tableData.searchInput.val(""); // Clear input first
    tableData.instance.search(""); // Clear DataTable search

    // Apply new search
    setTimeout(() => {
      tableData.searchInput.val(scannedCode);
      tableData.instance.search(scannedCode).draw();

      // Visual feedback
      this.showScanFeedback(scannedCode, this.currentFocusedTable);
    }, 50);

    this.clearScanBuffer();
  }

  /**
   * Show visual feedback for successful scan
   */
  showScanFeedback(scannedCode, tableId) {
    const tableData = this.activeTables.get(tableId);
    const container = tableData.container;

    // Create feedback element
    const feedback = $(`
            <div class="scan-feedback" style="
                position: absolute;
                top: 16px;
                right: 1px;
                background: #10B981;
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: slideInRight 0.3s ease-out;
            ">
                <i class="fas fa-barcode mr-2"></i>
                Scanned: ${scannedCode}
            </div>
        `);

    // Add CSS animation if not exists
    if (!$("#scan-feedback-styles").length) {
      $("head").append(`
                <style id="scan-feedback-styles">
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                .scanner-active {
                    position: relative;
                }
                </style>
            `);
    }

    container.css("position", "relative");
    container.append(feedback);

    // Remove feedback after 3 seconds
    setTimeout(() => {
      feedback.fadeOut(300, function () {
        $(this).remove();
      });
    }, 3000);
  }

  /**
   * Clear scan buffer
   */
  clearScanBuffer() {
    this.scanBuffer = "";
    if (this.scanTimeout) {
      clearTimeout(this.scanTimeout);
      this.scanTimeout = null;
    }
  }

  /**
   * Add new table to scanner management
   */
  addTable(tableId) {
    if ($(`#${tableId}`).length && $.fn.DataTable.isDataTable(`#${tableId}`)) {
      const dataTable = $(`#${tableId}`).DataTable();
      this.activeTables.set(tableId, {
        instance: dataTable,
        searchInput: $(`#${tableId}_filter input`),
        container: $(`#${tableId}`).closest(".dataTables_wrapper"),
      });

      // Setup focus detection for new table
      const tableData = this.activeTables.get(tableId);
      tableData.container.on("click", () => {
        this.setActiveTable(tableId);
      });

      tableData.searchInput.on("focus", () => {
        this.setActiveTable(tableId);
      });

      tableData.container.on("mouseenter", () => {
        this.setActiveTable(tableId);
      });

      console.log(`Scanner added for table: ${tableId}`);
      return true;
    }
    return false;
  }

  /**
   * Remove table from scanner management
   */
  removeTable(tableId) {
    if (this.activeTables.has(tableId)) {
      this.activeTables.delete(tableId);
      if (this.currentFocusedTable === tableId) {
        // Set another table as active if available
        if (this.activeTables.size > 0) {
          const firstTableId = this.activeTables.keys().next().value;
          this.setActiveTable(firstTableId);
        } else {
          this.currentFocusedTable = null;
        }
      }
      console.log(`Scanner removed for table: ${tableId}`);
      return true;
    }
    return false;
  }

  /**
   * Get list of managed tables
   */
  getManagedTables() {
    return Array.from(this.activeTables.keys());
  }

  /**
   * Manually trigger scan processing (for testing)
   */
  manualScan(barcode, tableId = null) {
    if (tableId && this.activeTables.has(tableId)) {
      this.setActiveTable(tableId);
    }

    this.scanBuffer = barcode;
    this.processScan();
  }
}

// Auto-initialize when document is ready
$(document).ready(function () {
  // Wait for DataTables to be fully initialized
  setTimeout(() => {
    window.scannerManager = new DataTableScannerManager();
    console.log("DataTable Scanner Manager initialized");
    console.log("Managed tables:", window.scannerManager.getManagedTables());
  }, 500);
});

// Export for manual initialization if needed
window.DataTableScannerManager = DataTableScannerManager;
