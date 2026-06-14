/**
 * JavaScript untuk Import Barang KI
 */
class BarangKiImporter {
  constructor() {
    this.modal = document.getElementById("importModal-excel");
    this.form = document.getElementById("importForm");
    this.fileInput = document.getElementById("file");
    this.importButton = document.getElementById("importButton");
    this.feedback = document.getElementById("importFeedback");
    this.progressContainer = document.getElementById("progressContainer");
    this.progressBar = document.getElementById("importProgress");
    this.progressText = document.getElementById("progressText");
    this.currentImportId = null;
    this.statusCheckInterval = null;

    this.init();
  }

  init() {
    this.bindEvents();
    this.setupFormValidation();
  }

  bindEvents() {
    // Form submit
    if (this.form) {
      this.form.addEventListener("submit", (e) => this.handleSubmit(e));
    }

    // File input change
    if (this.fileInput) {
      this.fileInput.addEventListener("change", (e) =>
        this.handleFileChange(e)
      );
    }
  }

  setupFormValidation() {
    if (!this.fileInput) return;

    this.fileInput.addEventListener("change", () => {
      const file = this.fileInput.files[0];
      if (!file) return;

      // Validate file type
      const allowedTypes = [
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-excel",
      ];

      if (!allowedTypes.includes(file.type)) {
        this.showError("Format file harus .xlsx atau .xls");
        this.fileInput.value = "";
        return;
      }

      // Validate file size (5MB max)
      const maxSize = 5 * 1024 * 1024; // 5MB in bytes
      if (file.size > maxSize) {
        this.showError("Ukuran file maksimal 5MB");
        this.fileInput.value = "";
        return;
      }

      this.clearFeedback();
    });
  }

  async handleSubmit(e) {
    e.preventDefault();

    const file = this.fileInput.files[0];
    if (!file) {
      this.showError("Silakan pilih file terlebih dahulu");
      return;
    }

    this.setLoading(true);
    this.clearFeedback();
    this.showProgressContainer(true);

    try {
      // Simulate upload progress
      await this.simulateImport();

      this.showSuccess("Import berhasil diselesaikan!");
      setTimeout(() => {
        this.closeModal();
      }, 2000);
    } catch (error) {
      console.error("Import error:", error);
      this.showError("Terjadi kesalahan saat mengimport data");
    } finally {
      this.setLoading(false);
    }
  }

  async simulateImport() {
    // Simulate import process with progress
    for (let i = 0; i <= 100; i += 10) {
      await new Promise((resolve) => setTimeout(resolve, 200));
      this.updateProgress(i);

      if (i === 50) {
        this.showInfo("Memvalidasi data...");
      } else if (i === 80) {
        this.showInfo("Menyimpan ke database...");
      }
    }
  }

  updateProgress(progress) {
    if (this.progressBar) {
      this.progressBar.style.width = `${progress}%`;
      this.progressBar.setAttribute("aria-valuenow", progress);
    }

    if (this.progressText) {
      this.progressText.textContent = `${Math.round(progress)}%`;
    }

    // Update feedback text
    if (progress > 0 && progress < 100) {
      this.showInfo(`Memproses data... ${Math.round(progress)}%`);
    }
  }

  handleDownloadTemplate(e, templateLink) {
    e.preventDefault();

    try {
      this.showInfo("Mengunduh template...");

      // Langsung navigasi ke URL download template
      window.location.href = templateLink;

      setTimeout(() => {
        this.showSuccess("Template berhasil diunduh!");
        setTimeout(() => this.clearFeedback(), 3000);
      }, 1500);
    } catch (error) {
      console.error("Download error:", error);
      this.showError("Gagal mengunduh template");
    }
  }

  handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
      this.showInfo(
        `File terpilih: ${file.name} (${this.formatFileSize(file.size)})`
      );
    }
  }

  setLoading(loading) {
    if (this.importButton) {
      this.importButton.disabled = loading;

      const icon = this.importButton.querySelector("i");
      const text = this.importButton.querySelector("span");

      if (loading) {
        if (icon) {
          icon.className = "fas fa-spinner fa-spin mr-2";
        }
        if (text) text.textContent = "Mengimport...";
      } else {
        if (icon) {
          icon.className = "fas fa-upload mr-2";
        }
        if (text) text.textContent = "Import Data";
      }
    }
  }

  showProgressContainer(show) {
    if (this.progressContainer) {
      if (show) {
        this.progressContainer.classList.remove("hidden");
      } else {
        this.progressContainer.classList.add("hidden");
      }
    }
  }

  showSuccess(message) {
    this.showFeedback(message, "success");
  }

  showError(message) {
    this.showFeedback(message, "error");
  }

  showWarning(message) {
    this.showFeedback(message, "warning");
  }

  showInfo(message) {
    this.showFeedback(message, "info");
  }

  showFeedback(message, type = "info") {
    if (!this.feedback) return;

    const colors = {
      success: "text-green-700 bg-green-50 border-green-200",
      error: "text-red-700 bg-red-50 border-red-200",
      warning: "text-yellow-700 bg-yellow-50 border-yellow-200",
      info: "text-blue-700 bg-blue-50 border-blue-200",
    };

    const icons = {
      success: "fas fa-check-circle",
      error: "fas fa-exclamation-circle",
      warning: "fas fa-exclamation-triangle",
      info: "fas fa-info-circle",
    };

    this.feedback.className = `p-4 rounded-lg border flex items-start space-x-3 ${colors[type]}`;
    this.feedback.innerHTML = `
            <i class="${icons[type]} mt-0.5"></i>
            <span class="flex-1">${message}</span>
        `;
    this.feedback.classList.remove("hidden");
  }

  clearFeedback() {
    if (this.feedback) {
      this.feedback.classList.add("hidden");
      this.feedback.innerHTML = "";
    }
  }

  closeModal() {
    if (this.modal) {
      this.modal.classList.add("hidden");
      this.modal.classList.remove("flex");
    }

    // Reset form and state
    this.resetForm();
  }

  resetForm() {
    if (this.form) {
      this.form.reset();
    }

    this.clearFeedback();
    this.setLoading(false);
    this.showProgressContainer(false);
    this.updateProgress(0);
    this.currentImportId = null;
  }

  formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";

    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  }

  formatDate(date) {
    return date.toISOString().split("T")[0];
  }
}

// Global functions for modal control
function handleImport() {
  console.log("Import button clicked");
  const modal = document.getElementById("importModal-excel");
  if (modal) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  }
}

function closeImportModal() {
  const modal = document.getElementById("importModal-excel");
  if (modal) {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  }

  // Reset importer if exists
  if (window.barangKiImporter) {
    window.barangKiImporter.resetForm();
  }
}

// Handle click outside modal to close
document.addEventListener("click", function (e) {
  const modal = document.getElementById("importModal-excel");
  if (e.target === modal) {
    closeImportModal();
  }
});

// Handle ESC key to close modal
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    const modal = document.getElementById("importModal-excel");
    if (modal && !modal.classList.contains("hidden")) {
      closeImportModal();
    }
  }
});

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize importer
  window.barangKiImporter = new BarangKiImporter();
  console.log("BarangKiImporter initialized");
});
