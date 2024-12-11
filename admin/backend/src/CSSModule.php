<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

final class CSSModule
{
    /**
     * @param array<string, string> $rawModule
     */
    public function __construct(
        private array $rawModule
    ) {
    }

    public function classFor(string $rawName): string
    {
        return $this->rawModule[$rawName];
    }
}
