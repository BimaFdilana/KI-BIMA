import "./bootstrap.js";
import _ from "lodash";
import Dropzone from "dropzone";
import axios from "axios";
import 'keen-slider/keen-slider.min.css'
import KeenSlider from 'keen-slider'
import Chart from 'chart.js/auto';

// Make available globally
window._ = _;
window.axios = axios;
window.Dropzone = Dropzone;
window.Chart = Chart;

// Configure axios defaults
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}

// Disable Dropzone auto-discover to prevent conflicts
Dropzone.autoDiscover = false;

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize Preline UI if available
  if (typeof window.HSStaticMethods !== "undefined") {
    window.HSStaticMethods.autoInit();
  }

  // Initialize custom dropzones
  initializeDropzones();
});

// Alpine.js integration
document.addEventListener("alpine:init", () => {
  Alpine.data("appData", () => ({
    init() {
      console.log("Lodash version:", _.VERSION);
    },
    // Example method using lodash
    processData(data) {
      return _.uniqBy(data, "id");
    },
  }));
});

// Function to initialize custom dropzones
function initializeDropzones() {
  const dropzoneElements = document.querySelectorAll(".dropzone-custom");

  dropzoneElements.forEach((element) => {
    if (!element.dropzone) {
      new Dropzone(element, {
        url: element.dataset.url || "/upload",
        maxFilesize: 5,
        acceptedFiles: "image/*,.pdf,.doc,.docx",
        addRemoveLinks: true,
        dictDefaultMessage: "Drop files here or click to upload",
        success: function (file, response) {
          console.log("Upload successful:", response);
          const processedData = _.pick(response, ["id", "filename", "url"]);
          console.log("Processed data:", processedData);
        },
        error: function (file, errorMessage) {
          console.error("Upload error:", errorMessage);
        },
      });
    }
  });
}
