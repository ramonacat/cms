<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use FastRoute\GenerateUri;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Template;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\ChildView;

final class BlocksView implements ChildView
{
    /**
     * @param list<Block> $blocks
     */
    public function __construct(
        public readonly GenerateUri $uriGenerator,
        public readonly array $blocks,
    ) {
    }

    public function loadTemplate(
        CSSModuleLoader $cssModuleLoader,
        FrontendModuleLoader $frontendModuleLoader,
        TemplateFactory $templateFactory
    ): Template {
        return $templateFactory->create('blocks/index.php', $this);
    }

    public function requiredFrontendModules(): array
    {
        return [];
    }
}
