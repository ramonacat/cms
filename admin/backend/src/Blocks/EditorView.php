<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Template;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\ChildView;

final class EditorView implements ChildView
{
    public function __construct(
        public readonly string $initialContent
    ) {
    }

    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template {
        return $templateFactory->create('blocks/editor.php', $this);
    }

    public function requiredFrontendModules(): array
    {
        return ['blocks/editor'];
    }
}
