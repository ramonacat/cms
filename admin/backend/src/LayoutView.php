<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModule;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\UI\View;

final class LayoutView implements View
{
    /**
     * @var list<FrontendModule>
     */
    private array $frontendModules = [];

    /**
     * @template T of object
     * @param Template<T> $body
     * @param list<string> $frontendModuleNames
     */
    public function __construct(
        public Template $body,
        private array $frontendModuleNames
    ) {
    }

    public function loadTemplate(CSSModuleLoader $cssModuleLoader, FrontendModuleLoader $frontendModuleLoader, TemplateFactory $templateFactory): Template
    {
        $this->frontendModules = array_map(fn ($name) => $frontendModuleLoader->load($name), $this->frontendModuleNames);

        return $templateFactory->create('layout.php', $this);
    }

    /**
     * @return list<FrontendModule>
     */
    public function frontendModules(): array
    {
        return $this->frontendModules;
    }
}
