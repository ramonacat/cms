<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Ramona\CMS\Admin\Authentication\Services\LoggedInUser;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\UI\View;

final class HomeView implements View
{
    public function __construct(
        public readonly LoggedInUser $loggedInUser
    ) {
    }

    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template {
        return $templateFactory->create('home.php', $this);
    }
}
