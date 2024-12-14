import { resolveConfig } from "vite";
import { generateModules } from "./gen-modules";
import viteConfig from "./vite.config";

const config = await resolveConfig(viteConfig, "build");
generateModules(config);
