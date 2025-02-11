<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Frontend;

use RuntimeException;
use function Safe\file_get_contents;
use function Safe\json_decode;

final readonly class FrontendModuleLoader
{
    public function __construct(
        private string $baseUrl,
        private string $manifestPath,
        private CSSModuleLoader $cssModuleLoader
    ) {
    }

    public function load(string $name): FrontendModule
    {
        $manifest = json_decode(file_get_contents($this->manifestPath), true);
        /** @var array<string, array<mixed>> $manifest */

        foreach ($manifest as $key => $module) {
            if ($module['name'] !== $name) {
                continue;
            }

            $javascriptFiles = [];

            // Modules that have a CSS entry point get an empty js file generated by vite.
            // There is no need to include this file.
            if (! str_ends_with($module['src'], '.css')) {
                $javascriptFiles[] = $this->baseUrl . '/' . $module['file'];
            }

            $cssFiles = array_map(
                fn (string $path) => $this->baseUrl . '/' . $path,
                $module['css']
            );
            assert(array_is_list($cssFiles));

            return new FrontendModule(
                $key,
                $cssFiles,
                $javascriptFiles,
                $this->cssModuleLoader->load($name)
            );
        }

        throw new RuntimeException("Module {$name} not found");
    }
}
