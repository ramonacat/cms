<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\UI\ChildView;

final class HomeView implements ChildView
{
    public function __construct(
    ) {
    }

    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template {
        return $templateFactory->create('home.php', $this);
    }

    public function requiredFrontendModules(): array
    {
        return [];
    }
}
