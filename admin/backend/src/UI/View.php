<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\UI;

use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Template;
use Ramona\CMS\Admin\TemplateFactory;

interface View
{
    /**
     * @return Template<View>
     */
    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template;
}
