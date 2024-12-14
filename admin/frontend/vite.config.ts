import { defineConfig, ResolvedConfig } from "vite";
import { generateModules } from "./gen-modules";
import react from "@vitejs/plugin-react";

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    react(),
    (() => {
      let config: ResolvedConfig | null = null;
      return {
        name: "regen-modules-on-hmr",
        configResolved: (resolvedConfig: ResolvedConfig) => {
          config = resolvedConfig;
        },
        handleHotUpdate: () => {
          generateModules(config!);
        },
      };
    })(),
  ],
  build: {
    manifest: true,
    rollupOptions: {
      input: {
        main: "./src/main.ts",
        login: "./src/login.module.css",
      },
    },
  },
});
