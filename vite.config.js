import { defineConfig } from "vite";
import { resolve } from "path";
import liveReload from "vite-plugin-live-reload";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    tailwindcss(),
    liveReload("./**/*.php"),
  ],

  resolve: {
    alias: {
      "@": resolve(import.meta.dirname, "./src"),
    },
  },

  root: "",
  base: process.env.NODE_ENV === "development" ? "/" : "./",

  build: {
    outDir: resolve(import.meta.dirname, "./dist"),
    emptyOutDir: true,
    manifest: true,
    target: "es2018",

    rollupOptions: {
      input: [resolve(import.meta.dirname, "/src/theme.js")],
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.names?.[0]?.endsWith(".woff2")) {
            return "assets/fonts/[name][extname]";
          }
          return "assets/[name]-[hash][extname]";
        },
      },
    },

    minify: true,
    write: true,
  },

  optimizeDeps: {
    include: ["@hotwired/turbo", "alpinejs"],
  },

  server: {
    cors: true,
    strictPort: true,
    port: 3000,
    host: "localhost",
    https: false,
    origin: "http://localhost:3000",

    fs: {
      strict: false,
      allow: [".."],
    },

    hmr: {
      host: "localhost",
      protocol: "ws",
    },
  },
});
