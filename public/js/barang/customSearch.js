document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("subcategorySearch");
  const subcategoryList = document.getElementById("subcategoryList");
  const noResults = document.getElementById("noResults");
  const subcategoryItems = document.querySelectorAll(".subcategory-item");

  // Search functionality
  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase().trim();
    let hasVisibleItems = false;

    subcategoryItems.forEach((item) => {
      const itemName = item.getAttribute("data-name");
      const isVisible = itemName.includes(searchTerm);

      item.style.display = isVisible ? "block" : "none";
      if (isVisible) hasVisibleItems = true;
    });

    // Show/hide no results message
    if (hasVisibleItems) {
      subcategoryList.style.display = "block";
      noResults.style.display = "none";
    } else {
      subcategoryList.style.display = "none";
      noResults.style.display = "block";
    }
  });

  // Clear search when dropdown is closed
  const dropdownButton = document.getElementById("subcategoryDropdownButton");
  const dropdown = document.getElementById("subcategoryDropdown");

  // Reset search when dropdown closes
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      if (
        mutation.type === "attributes" &&
        mutation.attributeName === "class"
      ) {
        if (dropdown.classList.contains("hidden")) {
          searchInput.value = "";
          subcategoryItems.forEach((item) => {
            item.style.display = "block";
          });
          subcategoryList.style.display = "block";
          noResults.style.display = "none";
        }
      }
    });
  });

  observer.observe(dropdown, {
    attributes: true,
  });

  // Focus search input when dropdown opens
  dropdownButton.addEventListener("mouseenter", function () {
    setTimeout(() => {
      if (!dropdown.classList.contains("hidden")) {
        searchInput.focus();
      }
    }, 100);
  });
});
