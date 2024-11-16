<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

final readonly class LayoutView
{
    /**
     * @template T of object
     * @param Template<T> $body
     */
    public function __construct(
        public Template $body
    ) {
    }
}