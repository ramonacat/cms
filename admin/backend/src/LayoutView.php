<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Ramona\CMS\Admin\Frontend\FrontendModule;

final readonly class LayoutView
{
    /**
     * @template T of object
     * @param Template<T> $body
     * @param list<FrontendModule> $frontendModules
     */
    public function __construct(
        public Template $body,
        public array $frontendModules
    ) {
    }
}
