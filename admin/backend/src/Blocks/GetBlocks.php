<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use FastRoute\GenerateUri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramona\CMS\Admin\Blocks\Services\Blocks;
use Ramona\CMS\Admin\UI\LayoutRenderer;

final class GetBlocks implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'blocks.index';

    public function __construct(
        private readonly LayoutRenderer $layoutRenderer,
        private readonly Blocks $blocks,
        private readonly GenerateUri $uriGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $blocksView = new BlocksView($this->uriGenerator, $this->blocks->all());

        return $this->layoutRenderer->render($blocksView, $request);
    }
}
