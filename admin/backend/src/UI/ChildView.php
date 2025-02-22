<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\UI;

interface ChildView extends View
{
    /**
     * @return list<string>
     */
    public function requiredFrontendModules(): array;
}
