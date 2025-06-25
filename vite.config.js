import { defineConfig } from "vite";
import liveReload from "vite-plugin-live-reload";
const { resolve } = require("path");
const fs = require("fs");

// https://vitejs.dev/config
export default defineConfig({
  plugins: [
    //vue(),
    liveReload(__dirname + "/**/*.php"),
  ],
  resolve: {
    alias: {
      "@": resolve(__dirname, "./src"),
    },
  },
  // config
  root: "",
  base: process.env.NODE_ENV === "development" ? "/" : "/dist/",

  build: {
    // output dir for production build
    outDir: resolve(__dirname, "./dist"),
    emptyOutDir: true,

    // emit manifest so PHP can find the hashed files
    manifest: true,

    // esbuild target
    target: "es2018",

    // our entry
    rollupOptions: {
      input: [resolve(__dirname + "/src/theme.js")],
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".woff2")) {
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
    host: "localhost", // Cambia a localhost en lugar de 0.0.0.0 // Add this to allow external access
    // serve over http
    https: false,
    origin: "http://localhost:3000",
    fs: {
      strict: false,
      allow: [".."],
    },

    hmr: {
      host: "localhost",
      //port: 443
      protocol: "ws", // Add websocket protocol
    },
  },
});
