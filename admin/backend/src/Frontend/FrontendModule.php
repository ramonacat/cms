<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Frontend;

final readonly class FrontendModule
{
    /**
     * @param list<string> $cssFiles
     * @param list<string> $jsFiles
     */
    public function __construct(
        public string $key,
        public array $cssFiles,
        public array $jsFiles,
        public ?CSSModule $cssModule
    ) {
    }
}
