import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: ["resources/views/**/*"],
    }),
    tailwindcss(),
  ],
  server: {
    cors: true,
    host: true, // Allow external connections
    port: 5173,
    strictPort: true,
    hmr: {
      host: "localhost",
    },
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ["lodash", "axios", "dropzone"],
        },
      },
    },
  },
  publicDir: "public",
});
