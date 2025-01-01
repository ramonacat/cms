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

    public function load(string $name): CSSModule
    {
        $rawModule = file_get_contents($this->basePath . '/' . $name . '.json');
        /** @var array<string,string> $moduleData */
        $moduleData = json_decode($rawModule, true);

        return new CSSModule($moduleData);
    }
}
