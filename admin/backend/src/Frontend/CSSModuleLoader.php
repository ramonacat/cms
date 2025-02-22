<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Frontend;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class CSSModuleLoader
{
    public function __construct(
        private string $basePath
    ) {
    }

    public function load(string $name): ?CSSModule
    {
        $modulePath = $this->basePath . '/' . $name . '.json';

        if (! file_exists($modulePath)) {
            return null;
        }

        $rawModule = file_get_contents($modulePath);
        /** @var array<string,string> $moduleData */
        $moduleData = json_decode($rawModule, true);

        return new CSSModule($moduleData);
    }
}
