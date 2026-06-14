class FormDetailManager {
  constructor() {
    this.init();
    this.setupEventListeners();
    this.barangEditID = null;
  }

  init() {
    // DOM Elements
    this.elements = {
      // Form elements
      actionBtnMessage: document.getElementById("actionBtnMessage"),
      closeBtnMessage: document.getElementById("closeBtnMessage"),
    };
  }

  setupEventListeners() {
    // Event listeners
    this.elements.actionBtnMessage.addEventListener("click", () =>
      this.showModal()
    );
    this.elements.closeBtnMessage.addEventListener("click", () =>
      this.closeModal()
    );
  }

  showModal() {
    // Show modal
    this.elements.modalDetailBarang.classList.remove("hidden");
    this.elements.modalDetailBarang.classList.add("block");
  }

  closeModal() {
    // Close modal
    this.elements.modalDetailBarang.classList.remove("block");
    this.elements.modalDetailBarang.classList.add("hidden");
  }
}
class DataTableManager {
  constructor() {
    this.currentAction = null;
    this.currentBarcode = null; // Untuk menyimpan barcode saat reload
    this.init();
    this.setupEventListeners();
    this.dataTable = null;
    this.mainTable = null;
    this.barangDetailID = null;
  }

  init() {
    // DOM Elements
    this.elements = {
      // Form elements
      actionBtnMessage: document.getElementById("actionBtnMessage"),
      closeBtnMessage: document.getElementById("closeBtnMessage"),

      // Modal elements
      modalDetailBarang: document.getElementById("modalDetailBarang"),
      modalDetailBarangTitle: document.getElementById("modalDetailBarangTitle"),
      expiredTime: document.getElementById("expiredTime"),
      expiredTimeValue: document.getElementById("expiredTimeValue"),
      expiredTimeBadge: document.getElementById("expiredTimeBadge"),
      expiredTimeBadgeDetail: document.getElementById("expiredTimeBadgeDetail"),
      summaryCards: document.querySelector("#summaryCards"),
      totalStock: document.querySelector("#totalStock"),
      totalAvailable: document.querySelector("#totalAvailable"),
      totalAvailableBadge: document.querySelector("#totalAvailableBadge"),
      totalSold: document.querySelector("#totalSold"),
      closeBtn: document.querySelector("#closeModalBtn"),
      closeBtnFooter: document.querySelector("#closeModalBtnFooter"),
      messageModal: document.getElementById("message-modal"),
      loadingOverlay: document.getElementById("loadingOverlay"),
      modalTitle: document.getElementById("modalTitle"),
      modalMessage: document.getElementById("modalMessage"),
      modalIcon: document.getElementById("modalIcon"),
      barcode: document.getElementById("barcode"),

      //Modal Edit Elements
      editbarangModal: document.getElementById("edit-barang-modal"),
      editbarangButton: document.getElementById("edit-barang-detail"),
      editbarangForm: document.getElementById("edit-barangForm"),
      closeEditModal: document.getElementById("close-edit-modal"),
      closeEditModalFooter: document.getElementById("close-edit-modal-footer"),
      submitEditModal: document.getElementById("submit-edit-modal"),
      barangSelect: document.getElementById("barang-select"),
      barcodeEdit: document.getElementById("barcodeEdit"),
      satuanSelect: document.getElementById("satuan-select"),
      quantity: document.getElementById("quantity"),
      priceBuy: document.getElementById("price_buy"),
      priceSell: document.getElementById("price_sell"),
      priceUp: document.getElementById("price_up"),
      expired_time_date: document.getElementById("expired_time_date"),
      expired_time_time: document.getElementById("expired_time_time"),
      discountTypeRadios: document.querySelectorAll(
        'input[name="discount_type"]'
      ),
      discountInput: document.querySelector('input[name="discount"]'),
      discountStart: document.querySelector('input[name="discount_start"]'),
      discountEnd: document.querySelector('input[name="discount_end"]'),
      statusRadios: document.querySelectorAll('input[name="status"]'),
      cariBarangInput: document.querySelector("#cari-barang-input"),
    };

    // Add error checking for required elements
    if (
      !this.elements.modalDetailBarang ||
      !this.elements.modalDetailBarangTitle
    ) {
      console.error("Required modal elements not found");
      return;
    }
  }

  setupEventListeners() {
    // Modal button listeners
    this.elements.closeBtnMessage?.addEventListener("click", () =>
      this.closeModal()
    );

    // Event delegation for DataTable buttons (use document body for better compatibility)
    document.body.addEventListener("click", (e) => {
      if (e.target.closest(".edit-barang-button")) {
        e.preventDefault();
        this.handleEdit(e);
      } else if (e.target.closest(".view-barang-button")) {
        e.preventDefault();
        this.handleView(e);
      } else if (e.target.closest(".delete-barang-button")) {
        e.preventDefault();
        this.handleDelete(e);
      } else if (e.target.closest(".restore-barang-button")) {
        e.preventDefault();
        this.handleRestore(e);
      } else if (e.target.closest(".delete-barang-detail")) {
        e.preventDefault();
        this.handleDeleteDetail(e);
      } else if (e.target.closest(".edit-barang-detail")) {
        e.preventDefault();
        this.handleEditDetail(e);
      } else if (e.target.closest(".restore-barang-detail")) {
        e.preventDefault();
        this.handleRestoreDetail(e);
      }
    });

    this.elements.actionBtnMessage.addEventListener("click", () =>
      this.handleActionBtnConfirm()
    );
    this.elements.closeBtn.addEventListener("click", () =>
      this.closeModalDetailBarang()
    );
    this.elements.closeBtnFooter.addEventListener("click", () =>
      this.closeModalDetailBarang()
    );
    this.elements.closeEditModal.addEventListener("click", () =>
      this.closeModalEditBarang()
    );
    this.elements.closeEditModalFooter.addEventListener("click", () =>
      this.closeModalEditBarang()
    );
    this.elements.barangSelect?.addEventListener("change", (e) =>
      this.handleBarangChange(e)
    );
    this.elements.cariBarangInput?.addEventListener("input", (e) =>
      this.handleCariBarangInput(e)
    );
    this.elements.submitEditModal?.addEventListener("click", (e) =>
      this.handleSubmitEditModal(e)
    );

    // Close modal when clicking overlay
    this.elements.modalDetailBarang.addEventListener("click", (e) => {
      if (e.target === this.elements.modalDetailBarang) {
        this.closeModalDetailBarang();
      }
    });

    // Close modal with ESC key
    document.addEventListener("keydown", (e) => {
      if (
        e.key === "Escape" &&
        !this.elements.modalDetailBarang.classList.contains("hidden")
      ) {
        this.closeModalDetailBarang();
      }
    });
    document.addEventListener("keydown", (e) => {
      if (
        e.key === "Escape" &&
        !this.elements.messageModal.classList.contains("hidden")
      ) {
        this.closeModal();
      }
    });
  }

  // Method untuk reload data detail setelah action
  reloadDetailData() {
    if (this.currentBarcode) {
      // Re-fetch data untuk detail table
      this.elements.barcode.value = this.currentBarcode;
      this.getData(false);
    }
  }

  // Method untuk reload main table
  reloadMainTable() {
    if (this.mainTable) {
      this.mainTable.ajax.reload(null, false); // false = tetap di halaman yang sama
    }
  }

  // Perbaiki method deleteDetailItem
  deleteDetailItem(id) {
    this.showLoading(true);
    this.closeModal();

    $.ajax({
      url: `/barang/ki/destroy/${id}`,
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
      },
      success: (response) => {
        this.showLoading(false);
        this.showMessage("Success", response.message, "success");

        // Reload kedua tabel setelah sukses delete
        this.reloadMainTable(); // Reload tabel utama

        // Delay sedikit sebelum reload detail untuk memastikan data sudah terupdate
        setTimeout(() => {
          this.reloadDetailData(); // Reload detail table
        }, 500);
      },
      error: (error) => {
        this.showLoading(false);
        this.showMessage(
          "Error",
          error.responseJSON?.message || "Terjadi kesalahan",
          "error"
        );
      },
    });
  }

  // Tambahkan method untuk restore item
  restoreItem(id) {
    this.showLoading(true);
    this.closeModal();

    $.ajax({
      url: `/barang/ki/destroy/${id}`, // Sesuaikan dengan route restore Anda
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
      },
      success: (response) => {
        this.showLoading(false);
        this.showMessage("Success", response.message, "success");

        // Reload kedua tabel setelah sukses restore
        this.reloadMainTable(); // Reload tabel utama

        // Delay sedikit sebelum reload detail untuk memastikan data sudah terupdate
        setTimeout(() => {
          this.reloadDetailData(); // Reload detail table
        }, 500);
      },
      error: (error) => {
        this.showLoading(false);
        this.showMessage(
          "Error",
          error.responseJSON?.message || "Terjadi kesalahan",
          "error"
        );
      },
    });
  }

  // Perbaiki method handleActionBtnConfirm
  handleActionBtnConfirm() {
    const action = this.currentAction;
    const id = this.elements.barcode.value;
    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    switch (action) {
      case "delete":
        this.deleteDetailItem(id);
        break;
      case "restore":
        this.restoreItem(id);
        break;
      case "delete-detail":
        this.deleteDetailItem(id);
        break;
      case "restore-detail":
        this.restoreItem(id); // Gunakan restoreItem untuk restore-detail
        break;
    }
  }

  // Perbaiki method getData untuk menyimpan barcode
  getData(load = true) {
    const id = this.elements.barcode.value;
    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan untuk ", "error");
      return;
    }
    if (load) {
      this.showLoading(true);
    }
    this.currentBarcode = id; // Simpan barcode untuk reload nanti

    fetch(
      `/barang/ki/get-data-from-same-expired?barcode=${id}&action=${this.currentAction}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        this.showLoading(false);
        // Cek apakah ada error dari backend
        if (data.success === false) {
          this.showMessage("Error", data.error, "error");
          return;
        }

        // Pastikan data ada dan memiliki struktur yang benar
        if (!data.barangki.data || data.barangki.data.length === 0) {
          if (this.currentAction === "delete") {
            this.showMessage(
              "Info",
              "Tidak ada data untuk dihapus, Karna salah satu toko sudah membeli barang tersebut",
              "info"
            );
            return;
          } else {
            this.showMessage("Info", "Tidak ada data ditemukan", "info");
            return;
          }
        }
        // Set title modal dengan info barang pertama
        const firstItem = data.barangki.data[0];
        this.elements.modalDetailBarangTitle.textContent = `${firstItem.name}`;
        this.elements.expiredTimeValue.textContent = firstItem.expired_time;
        this.elements.expiredTimeBadge.innerHTML = firstItem.expiry_status;
        const today = new Date();
        const expiredDate = new Date(firstItem.expired_time_value);
        if (expiredDate < today) {
          this.elements.totalAvailableBadge.classList.remove("hidden");
          this.elements.expiredTimeBadgeDetail.classList.remove("hidden");
        } else {
          this.elements.totalAvailableBadge.classList.add("hidden");
          this.elements.expiredTimeBadgeDetail.classList.add("hidden");
        }

        // Update summary cards
        this.updateSummaryCards(data.summary);

        // Open modal
        this.openModal();

        // Initialize atau update DataTable
        this.initDataTable(data.barangki.data);

        // Reset barcode input hanya jika bukan untuk reload
        if (this.currentAction !== "reload") {
          this.elements.barcode.value = "";
        }
      })
      .catch((error) => {
        this.showLoading(false);
        console.error("Error:", error);
        this.showMessage(
          "Error",
          "Gagal mengambil detail barang: " + error.message,
          "error"
        );
      });
  }

  // Perbaiki method initMainTableReference
  initMainTableReference() {
    // Tunggu sampai DataTable utama siap
    const checkTable = () => {
      if ($.fn.DataTable.isDataTable("#barang-table-ki")) {
        this.mainTable = $("#barang-table-ki").DataTable();
        console.log("Main table reference initialized");
      } else {
        // Coba lagi setelah 100ms jika tabel belum siap
        setTimeout(checkTable, 100);
      }
    };

    setTimeout(checkTable, 500);
  }

  // Method untuk membuka modal
  openModal() {
    this.elements.modalDetailBarang.classList.remove("hidden");
    this.elements.modalDetailBarang.classList.add("flex");
    document.body.style.overflow = "hidden"; // Prevent body scroll
  }

  // Method untuk menutup modal
  closeModalDetailBarang() {
    this.elements.modalDetailBarang.classList.add("hidden");
    this.elements.modalDetailBarang.classList.remove("flex");
    document.body.style.overflow = ""; // Restore body scroll

    // Destroy DataTable if exists
    if (this.dataTable) {
      this.dataTable.destroy();
      this.dataTable = null;
    }

    // Hide summary cards
    const summaryCards = document.getElementById("summaryCards");
    if (summaryCards) {
      summaryCards.classList.add("hidden");
    }
  }

  // Method untuk update summary cards
  updateSummaryCards(summary) {
    if (summary) {
      const summaryCards = document.getElementById("summaryCards");
      const totalStock = document.getElementById("totalStock");
      const totalAvailable = document.getElementById("totalAvailable");
      const totalSold = document.getElementById("totalSold");

      if (summaryCards && totalStock && totalAvailable && totalSold) {
        totalStock.textContent = this.formatNumber(summary.total_stock || 0);
        totalAvailable.textContent = this.formatNumber(
          summary.total_available || 0
        );
        totalSold.textContent = this.formatNumber(summary.total_sold || 0);
        summaryCards.classList.remove("hidden");
      }
    }
  }

  // Method untuk format number
  formatNumber(num) {
    return new Intl.NumberFormat("id-ID").format(num);
  }

  // Method untuk initialize DataTable
  initDataTable(data) {
    // Destroy existing DataTable if exists
    if (this.dataTable) {
      this.dataTable.destroy();
    }

    // Initialize DataTable
    this.dataTable = $("#detailBarangTable").DataTable({
      data: data,
      columns: [
        {
          data: "barcode",
          title: "Barcode",
        },
        {
          data: "quantity",
          title: "Quantity",
        },
        {
          data: "price_buy",
          title: "Harga Beli",
        },
        {
          data: "price_sell",
          title: "Harga Jual",
        },
        {
          data: "discount",
          title: "Diskon",
          render: function (discount) {
            let statusClass = "bg-gray-100 text-gray-800";
            let statusText = "-";
            if (discount.is_discounted) {
              statusClass = "bg-green-100 text-green-800";
              statusText = `Diskon ${Math.round(
                discount.discount_percentage
              )}%`;
            } else if (!discount.is_discounted) {
              statusClass = "bg-yellow-100 text-yellow-800";
              statusText = "Tidak Diskon";
            }

            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${statusText}</span>`;
          },
        },
        {
          data: "status_badge",
          title: "Status",
        },
        {
          data: null,
          title: "Action",
          width: "200px",
          render: function (data, type, row) {
            let buttons = '<div class="flex space-x-1">';
            if (row.deleted_at) {
              buttons += `
    <button tooltip title="Restore" data-id="${row.barcode}" type="button" 
            class="restore-barang-detail rounded bg-green-100 text-green-600 hover:text-green-600 hover:bg-green-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex items-center">
        <i class="fad fa-undo"></i>
    </button>
`;
            } else {
              // View Button
              if (row.can_view) {
                buttons += `
              <button tooltip title="View" type="button"
                    class="rounded bg-blue-100 text-blue-600 hover:text-blue-600 hover:bg-blue-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex items-center"
                   onclick="window.location.href = '/barang/ki/detail/${row.barcode}'">
                <i class="fad fa-eye"></i>
            </button>
            `;
              }

              // Edit Button
              if (row.can_edit) {
                buttons += `
                <button tooltip title="Edit" data-id="${row.barcode}" type="button" 
                        class="edit-barang-detail rounded bg-yellow-100 text-yellow-600 hover:text-yellow-600 hover:bg-yellow-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex items-center">
                    <i class="fad fa-pen"></i>
                </button>
            `;
              }

              // Delete Button - hanya jika user punya permission dan barangtoko kosong
              if (row.can_delete && row.barangtoko_count === 0) {
                buttons += `
                <button tooltip title="Delete" data-id="${row.barcode}" type="button" 
                        class="delete-barang-detail rounded bg-red-100 text-red-600 hover:text-red-600 hover:bg-red-200 text-sm font-medium px-2 py-1.5 cursor-pointer hover:scale-125 transition-all inline-flex items-center">
                    <i class="fad fa-trash"></i>
                </button>
            `;
              }
            }
            buttons += "</div>";
            return buttons;
          },
        },
      ],
      responsive: true,
      pageLength: 10,
      lengthMenu: [
        [5, 10, 25, 50],
        [5, 10, 25, 50],
      ],
      language: {
        sProcessing: "Sedang memproses...",
        sLengthMenu: "Tampilkan _MENU_ entri",
        sZeroRecords: "Tidak ditemukan data yang sesuai",
        sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
        sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
        sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
        sInfoPostFix: "",
        sSearch: "Cari:",
        sUrl: "",
        oPaginate: {
          sFirst: "<<",
          sPrevious: "<",
          sNext: ">",
          sLast: ">>",
        },
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "copy",
          text: "Salin",
          className:
            "bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm",
        },
        {
          extend: "excel",
          text: "Export Excel",
          className:
            "bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm",
        },
        {
          extend: "pdf",
          text: "Export PDF",
          className:
            "bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm",
        },
      ],
      order: [[0, "desc"]], // Sort by first column (barcode) descending
      searching: true,
      ordering: true,
      info: true,
      paging: true,
    });
  }

  // Method untuk initialize event listeners (tambahkan ke constructor atau init method)
  initModalEventListeners() {
    // Close modal events
    const closeBtn = document.getElementById("closeModalBtn");
    const closeBtnFooter = document.getElementById("closeModalBtnFooter");

    if (closeBtn) {
      closeBtn.addEventListener("click", () => this.closeModal());
    }

    if (closeBtnFooter) {
      closeBtnFooter.addEventListener("click", () => this.closeModal());
    }

    // Close modal when clicking overlay
    if (this.elements.modalDetailBarang) {
      this.elements.modalDetailBarang.addEventListener("click", (e) => {
        if (e.target === this.elements.modalDetailBarang) {
          this.closeModal();
        }
      });
    }

    // Close modal with ESC key
    document.addEventListener("keydown", (e) => {
      if (
        e.key === "Escape" &&
        this.elements.modalDetailBarang &&
        !this.elements.modalDetailBarang.classList.contains("hidden")
      ) {
        this.closeModal();
      }
    });
  }

  handleView(e) {
    const button = e.target.closest(".view-barang-button");
    const id = button?.getAttribute("data-id");

    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    this.currentAction = "view";
    this.elements.barcode.value = id;
    this.getData();
  }

  handleEdit(e) {
    const button = e.target.closest(".edit-barang-button");
    const id = button?.getAttribute("data-id");

    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    this.currentAction = "edit";
    this.elements.barcode.value = id;
    this.getData();
  }

  handleDelete(e) {
    const button = e.target.closest(".delete-barang-button");
    const id = button?.getAttribute("data-id");
    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    this.currentAction = "delete";
    this.elements.barcode.value = id;
    this.getData();
  }

  handleRestore(e) {
    const button = e.target.closest(".restore-barang-button");
    const id = button?.getAttribute("data-id");

    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    this.currentAction = "restore";
    this.elements.barcode.value = id;
    this.showMessage(
      "Konfirmasi Restore",
      "Apakah Anda yakin ingin memulihkan item ini?",
      "warning"
    );

    // Update action button text
    this.elements.actionBtnMessage.textContent = "Restore";
    this.elements.actionBtnMessage.className =
      "cursor-pointer rounded-lg bg-green-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-green-600";
  }

  handleDeleteDetail(e) {
    const button = e.target.closest(".delete-barang-detail");
    const id = button?.getAttribute("data-id");
    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }
    this.currentAction = "delete-detail";
    this.elements.barcode.value = id;
    this.showModalAction(
      "Konfirmasi Hapus",
      "Hapus Data : " + id + "\nApakah Anda yakin ingin menghapus item ini?",
      "delete"
    );

    // Update action button text
    this.elements.actionBtnMessage.textContent = "Hapus";
    this.elements.actionBtnMessage.className =
      "cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600";
  }

  async handleEditDetail(e) {
    e.preventDefault();
    this.elements.barcodeEdit.value = e.target
      .closest(".edit-barang-detail")
      ?.getAttribute("data-id");
    this.showLoading(true);
    try {
      const response = await axios.post("/barang/ki/find-barcode", {
        barcode: this.elements.barcodeEdit.value,
      });

      if (!response.data.success) {
        throw new Error(response.data.message || "Gagal mengambil data");
      }

      const response2 = await axios.post("/barang/ki/get-barang-same-id", {
        barang_id: response.data.data.barang_id,
      });

      this.barangDetailID = response.data.data.barang_id;

      if (!response2.data.success) {
        throw new Error(response2.data.message || "Gagal mengambil data");
      }

      const satuan = await axios.post(`/barang/ki/get-satuan-convert-barang`, {
        barang_id: response.data.data.barang_id,
        satuan_id: response.data.data.satuan_id,
      });

      if (!satuan.data.success) {
        throw new Error(satuan.data.message || "Gagal mengambil data");
      }

      this.showLoading(false);
      this.barangEditID = response.data.data.id;
      this.showEditModal(response.data.data);
      this.updateBarangOptions(response2.data.data);
      this.updateSatuanOptions(satuan.data.data);
    } catch (error) {
      this.showLoading(false);
      showToast("error", error.message || "Gagal mengambil data", "Error!");
    }
  }

  async handleCariBarangInput(e) {
    const searchValue = e.target.value.toLowerCase();
    try {
      const response = await axios.post("/barang/ki/get-barang-same-id", {
        barang_id: this.barangDetailID,
        search: searchValue,
      });

      if (!response.data.success) {
        throw new Error(response.data.message || "Gagal mengambil data");
      }

      this.updateBarangOptions(response.data.data);
    } catch (error) {
      showToast("error", error.message || "Gagal mengambil data", "Error!");
    }
  }

  updateBarangOptions(barangData) {
    if (barangData && Array.isArray(barangData)) {
      const barangSelect = this.elements.barangSelect;
      if (barangSelect) {
        // Clear existing options except the first one
        while (barangSelect.children.length > 1) {
          barangSelect.removeChild(barangSelect.lastChild);
        }

        // Add new options
        barangData.forEach((barang) => {
          const option = document.createElement("option");
          option.value = barang.id;
          option.textContent = `${barang.name} - ${barang.sku}`;
          // Set selected state if it exists
          if (barang.selected) {
            option.selected = true;
          }
          // Get the main image URL or use a default box icon
          const imageUrl = barang.main_image_url
            ? `<img src="${barang.main_image_url}" class="w-5 h-5 object-cover rounded" />`
            : `<i class="fas fa-box text-gray-400 text-xs"></i>`;

          option.setAttribute(
            "data-hs-select-option",
            JSON.stringify({
              icon: `<div class="shrink-0">${imageUrl}</div>`,
              selected: barang.selected,
            })
          );

          barangSelect.appendChild(option);
        });

        // Trigger HSSelect update if using HSSelect
        if (window.HSSelect) {
          window.HSSelect.getInstance(barangSelect)?.destroy();
          window.HSSelect.autoInit();
        }
      }
    }
  }
  validateForm() {
    const requiredFields = [
      {
        element: this.elements.barangSelect,
        name: "Barang",
      },
      {
        element: this.elements.barcodeEdit,
        name: "Barcode",
      },
      {
        element: this.elements.satuanSelect,
        name: "Satuan",
      },
      {
        element: this.elements.quantity,
        name: "Quantity",
      },
      {
        element: this.elements.priceBuy,
        name: "Harga Beli",
      },
      {
        element: this.elements.priceSell,
        name: "Harga Jual",
      },
      {
        element: this.elements.priceUp,
        name: "Margin",
      },
      {
        element: this.elements.expired_time_date,
        name: "Expired Date",
      },
      {
        element: this.elements.expired_time_time,
        name: "Expired Time",
      },
    ];

    for (const field of requiredFields) {
      if (!field.element) {
        console.error(`Element not found: ${field.name}`);
        continue;
      }

      const value = field.element.value ? field.element.value.trim() : "";
      if (!value) {
        showToast("error", `${field.name} harus diisi`, "Validasi Gagal");
        field.element.focus();
        return false;
      }
    }

    // Validate numeric fields
    const quantity = document.getElementById("quantity");
    if (
      quantity &&
      (isNaN(quantity.value) || parseFloat(quantity.value) <= 0)
    ) {
      showToast(
        "error",
        "Quantity harus berupa angka positif",
        "Validasi Gagal"
      );
      quantity.focus();
      return false;
    }

    const priceBuy = this.parseCurrency(this.elements.priceBuy?.value || "0");
    const priceSell = this.parseCurrency(this.elements.priceSell?.value || "0");

    if (priceBuy <= 0) {
      showToast("error", "Harga beli harus lebih dari 0", "Validasi Gagal");
      this.elements.priceBuy?.focus();
      return false;
    }

    if (priceSell <= 0) {
      showToast("error", "Harga jual harus lebih dari 0", "Validasi Gagal");
      this.elements.priceSell?.focus();
      return false;
    }

    // Validate status selection
    const hasStatus = Array.from(this.elements.statusRadios).some(
      (radio) => radio.checked
    );
    if (!hasStatus) {
      showToast("error", "Pilih status barang", "Validasi Gagal");
      return false;
    }

    return true;
  }

  async handleSubmitEditModal(e) {
    e.preventDefault();

    if (!this.validateForm()) {
      return;
    }

    this.elements.submitEditModal.classList.add("disabled");
    this.elements.submitEditModal.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Loading...';
    this.elements.submitEditModal.disabled = true;

    const formData = new FormData(this.elements.editbarangForm);
    const data = Object.fromEntries(formData.entries());
    this.editModalData = data;

    await axios
      .put(`/barang/ki/update/${this.barangEditID}`, data)
      .then((response) => {
        this.elements.editbarangModal.classList.add("hidden");
        this.elements.editbarangModal.classList.remove("flex");
        this.showMessage("Success", response.data.message, "success");
        setTimeout(() => {
          this.reloadMainTable();
          this.reloadDetailData();
        }, 3000);
      })
      .catch((error) => {
        if (error.response.status === 422) {
          const errors = error.response.data.errors;
          const message = error.response.data.message;
          const errorMessages = Object.keys(errors)
            .map((key) => errors[key][0])
            .join("<br>");
          showToast("error", errorMessages, "Validasi Gagal");
        } else {
          showToast(
            "error",
            error.response.data.message || "Gagal mengupdate data",
            "Error!"
          );
        }
      })
      .finally(() => {
        this.elements.submitEditModal.classList.remove("disabled");
        this.elements.submitEditModal.innerHTML = "Simpan Perubahan";
        this.elements.submitEditModal.disabled = false;
      });
  }

  updateSatuanOptions(satuanData) {
    // This would update the satuan dropdown based on the response
    // Implementation depends on your API response structure
    if (satuanData && Array.isArray(satuanData)) {
      // Update satuan select options
      const satuanSelect = this.elements.satuanSelect;
      if (satuanSelect) {
        // Clear existing options except the first one
        while (satuanSelect.children.length > 1) {
          satuanSelect.removeChild(satuanSelect.lastChild);
        }

        // Add new options
        satuanData.forEach((satuan) => {
          const option = document.createElement("option");
          option.value = satuan.id;
          option.textContent = satuan.name;
          if (satuan.selected) {
            option.selected = true;
          }
          option.setAttribute(
            "data-hs-select-option",
            JSON.stringify({
              icon: `<div class="shrink-0 size-5 text-xs text-gray-500"><i class="fas fa-l"></i>${satuan.level}</div>`,
            })
          );
          satuanSelect.appendChild(option);
        });

        // Trigger HSSelect update if using HSSelect
        if (window.HSSelect) {
          window.HSSelect.getInstance(satuanSelect)?.destroy();
          window.HSSelect.autoInit();
        }
      }
    }
  }

  showEditModal(barangki) {
    this.elements.editbarangModal?.classList.remove("hidden");
    this.elements.editbarangModal?.classList.add("flex");

    this.elements.modalDetailBarang.classList.add("hidden");
    this.elements.modalDetailBarang.classList.remove("flex");

    this.elements.barcodeEdit.value = barangki.id_barcode;
    this.elements.quantity.value = barangki.quantity;
    this.elements.priceBuy.value = barangki.price_buy;
    this.elements.priceSell.value = barangki.price_sell;
    this.elements.priceUp.value = barangki.price_up;
    this.elements.expired_time_date.value = barangki.expired_time_date;
    this.elements.expired_time_time.value = barangki.expired_time_time;
    this.elements.discountStart.value = barangki.discount_start;
    this.elements.discountEnd.value = barangki.discount_end;
    this.elements.discountTypeRadios.forEach((radio) => {
      if (radio.value === barangki.discount_type) {
        radio.checked = true;
      }
    });
    this.elements.discountInput.value = barangki.discount;
    this.elements.statusRadios.forEach((radio) => {
      if (radio.value === barangki.status) {
        radio.checked = true;
      }
    });
  }

  async handleBarangChange(e) {
    const selectedValue = e.target.value;

    if (!selectedValue) return;

    this.showLoading(true);

    try {
      const satuan = await axios.post(`/barang/ki/get-satuan-convert-barang`, {
        barang_id: selectedValue,
      });

      if (!satuan.data.success) {
        throw new Error(satuan.data.message || "Gagal mengambil data");
      }

      this.showLoading(false);
      this.updateSatuanOptions(satuan.data.data);
    } catch (error) {
      this.showLoading(false);
      showToast("error", error.message || "Gagal mengambil data", "Error!");
    }
  }

  closeModalEditBarang() {
    this.elements.editbarangModal.classList.add("hidden");
    this.elements.editbarangModal.classList.remove("flex");
    this.elements.modalDetailBarang.classList.add("flex");
    this.elements.modalDetailBarang.classList.remove("hidden");
    this.barangEditID = null;
  }

  handleRestoreDetail(e) {
    const button = e.target.closest(".restore-barang-detail");
    const id = button?.getAttribute("data-id");
    if (!id) {
      this.showMessage("Error", "ID tidak ditemukan", "error");
      return;
    }

    this.currentAction = "restore-detail";
    this.elements.barcode.value = id;
    this.showModalAction(
      "Konfirmasi Restore",
      "Restore Data : " + id + "\nApakah Anda yakin ingin memulihkan item ini?",
      "restore"
    );

    // Update action button text
    this.elements.actionBtnMessage.textContent = "Restore";
    this.elements.actionBtnMessage.className =
      "cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600";
  }

  parseCurrency(value) {
    if (!value) return 0;

    // Remove dots (thousand separators) and replace comma with dot
    let cleanValue = value.toString().replace(/\./g, "").replace(",", ".");

    // Remove any non-numeric characters except dot
    cleanValue = cleanValue.replace(/[^\d.]/g, "");

    return parseFloat(cleanValue) || 0;
  }

  showLoading(show) {
    if (show) {
      this.elements.loadingOverlay?.classList.remove("hidden");
      this.elements.loadingOverlay?.classList.add("flex");
    } else {
      this.elements.loadingOverlay?.classList.add("hidden");
      this.elements.loadingOverlay?.classList.remove("flex");
    }
  }

  showMessage(title, message, type = "info") {
    const modal = this.elements.messageModal;
    const titleEl = this.elements.modalTitle;
    const messageEl = this.elements.modalMessage;
    const iconEl = this.elements.modalIcon;

    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;

    // Update icon based on type
    if (iconEl) {
      iconEl.className = "fas text-6xl ";
      switch (type) {
        case "success":
          iconEl.className += "fa-check-circle text-green-500";
          break;
        case "warning":
          iconEl.className += "fa-exclamation-triangle text-yellow-500";
          break;
        case "error":
          iconEl.className += "fa-times-circle text-red-500";
          break;
        default:
          iconEl.className += "fa-info-circle text-blue-500";
      }
    }

    // Show/hide action buttons based on type
    const actionButtons = document.getElementById("actionButtons");
    if (actionButtons) {
      if (type === "warning") {
        actionButtons.style.display = "flex";
      } else {
        actionButtons.style.display = "none";
      }
    }

    modal?.classList.remove("hidden");
    modal?.classList.add("flex");

    // Auto-close for success messages
    if (type === "success") {
      setTimeout(() => this.closeModal(), 3000);
    } else {
      setTimeout(() => this.closeModal(), 5000);
    }
  }

  showModalAction(title, message, type = "info") {
    const modal = this.elements.messageModal;
    const titleEl = this.elements.modalTitle;
    const messageEl = this.elements.modalMessage;
    const iconEl = this.elements.modalIcon;

    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;

    // Update icon based on type
    if (iconEl) {
      iconEl.className = "fas text-6xl ";
      switch (type) {
        case "success":
          iconEl.className += "fa-check-circle text-green-500";
          break;
        case "warning":
          iconEl.className += "fa-exclamation-triangle text-yellow-500";
          break;
        case "error":
          iconEl.className += "fa-times-circle text-red-500";
          break;
        case "delete":
          iconEl.className += "fa-trash text-red-500";
          break;
        case "restore":
          iconEl.className += "fa-rotate text-red-500";
          break;
        default:
          iconEl.className += "fa-info-circle text-blue-500";
      }
    }

    // Show/hide action buttons based on type
    const actionButtons = document.getElementById("actionButtons");
    if (actionButtons) {
      if (type === "delete" || type === "restore") {
        actionButtons.style.display = "flex";
      } else {
        actionButtons.style.display = "none";
      }
    }

    modal?.classList.remove("hidden");
    modal?.classList.add("flex");
  }

  closeModal() {
    this.elements.messageModal?.classList.add("hidden");
    this.elements.messageModal?.classList.remove("flex");
    this.elements.barcode.value = "";
  }
}
