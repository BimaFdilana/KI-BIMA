document.addEventListener("DOMContentLoaded", function () {
  let sidebarState = localStorage.getItem("sidebarState");
  let content = document.querySelector(".content-with-sidebar");

  function updateSidebar() {
    if (localStorage.getItem("sidebarState") === "open") {
      content.classList.add("md:ml-64");
      content.classList.remove("md:ml-20");
    } else {
      content.classList.add("md:ml-20");
      content.classList.remove("md:ml-64");
    }
  }
  updateSidebar();
  window.addEventListener("sidebar-toggle", function () {
    updateSidebar();
    setTimeout(() => {
      window.dispatchEvent(new Event("resize")); // Paksa resize event
    }, 300);
  });
});
