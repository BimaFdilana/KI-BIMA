class ExportData {
  constructor(exportUrl, format, filters = {}) {
    this.exportUrl = exportUrl;
    this.format = format;
    this.filters = filters;
  }

  // Instance method: export data
  async export() {
    try {
      const response = await axios.post(
        `/${this.exportUrl}/export/${this.format}`,
        {
          filters: this.filters,
        }
      );

      const data = response.data;

      if (!data.success) {
        throw new Error(data.message || "Gagal mengambil data");
      }

      showToast("success", data.message, "Sukses");

      // Optional: jika backend mengembalikan URL file untuk diunduh
      if (data.url) {
        const link = document.createElement("a");
        link.href = data.url;
        link.download = data.filename || `export.${this.format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    } catch (error) {
      showToast("error", error.message || "Gagal mengekspor data", "Error!");
    }
  }

  // Static method untuk import
  static import(type) {
    const formData = new FormData();
    const fileInput = document.getElementById("import-file-" + type);
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content");

    if (!fileInput || !fileInput.files.length) {
      Swal.fire("Error", "Pilih file terlebih dahulu!", "warning");
      return;
    }

    formData.append("file", fileInput.files[0]);

    fetch(`barang/toko/import/${type}`, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": csrfToken,
      },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        Swal.fire("Sukses", data.message, "success");
        $(`#importModal-${type}`).modal("hide");
        // Optional: reload DataTable
        if (typeof reloadTable === "function") reloadTable();
      })
      .catch((err) => {
        Swal.fire("Error", "Upload gagal: " + err.message, "error");
      });
  }
}

// Inisialisasi global (opsional)
let exportData;
