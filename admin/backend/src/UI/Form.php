<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\UI;

final class Form
{
    /**
     * @param list<string> $globalErrors
     */
    public function __construct(
        private array $globalErrors
    ) {
    }

    /**
     * @return list<string>
     */
    public function globalErrors(): array
    {
        return $this->globalErrors;
    }
}
