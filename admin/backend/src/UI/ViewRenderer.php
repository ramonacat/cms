<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\UI;

use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Template;
use Ramona\CMS\Admin\TemplateFactory;

final class ViewRenderer
{
    public function __construct(
        private readonly CSSModuleLoader $cssModuleLoader,
        private readonly FrontendModuleLoader $frontendModuleLoader,
        private readonly TemplateFactory $templateFactory,
    ) {
    }

    /**
     * @return Template<View>
     */
    public function render(View $view): Template
    {
        return $view->loadTemplate(
            $this->cssModuleLoader,
            $this->frontendModuleLoader,
            $this->templateFactory
        );
    }
}
