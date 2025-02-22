<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use Ramona\CMS\Admin\Frontend\CSSModule;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Template;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\Form;
use Ramona\CMS\Admin\UI\View;

final class LoginView implements View
{
    private CSSModule $cssModule;

    public function __construct(
        public readonly Form $form
    ) {
    }

    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template {
        $cssModule = $cssModuleLoader->load('login');

        assert($cssModule !== null);

        $this->cssModule = $cssModule;

        return $templateFactory->create('user/login.php', $this);
    }

    public function cssModule(): CSSModule
    {
        return $this->cssModule;
    }
}
