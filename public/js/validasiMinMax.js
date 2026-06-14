document.addEventListener("DOMContentLoaded", function () {
  const priceMinInput = document.getElementById("priceMin");
  const priceMaxInput = document.getElementById("priceMax");

  // Only allow numbers
  [priceMinInput, priceMaxInput].forEach((input) => {
    input.addEventListener("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  });

  // Validate price range
  priceMaxInput.addEventListener("input", function () {
    const minValue = parseInt(priceMinInput.value) || 0;
    const maxValue = parseInt(this.value) || 0;

    if (maxValue > 0 && maxValue < minValue) {
      this.setCustomValidity(
        "Harga maksimal harus lebih besar dari harga minimal"
      );
    } else {
      this.setCustomValidity("");
    }
  });

  // Form validation
  document
    .getElementById("priceFilterForm")
    .addEventListener("submit", function (e) {
      const minValue = parseInt(priceMinInput.value) || 0;
      const maxValue = parseInt(priceMaxInput.value) || 0;

      if (maxValue > 0 && maxValue < minValue) {
        e.preventDefault();
        showToast(
          "error",
          "Harga maksimal harus lebih besar dari harga minimal",
          "Error!"
        );
        return false;
      }

      // Remove empty values before submit
      if (!priceMinInput.value.trim()) {
        priceMinInput.removeAttribute("name");
      }
      if (!priceMaxInput.value.trim()) {
        priceMaxInput.removeAttribute("name");
      }
    });
});
