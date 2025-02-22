<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramona\CMS\Admin\Blocks\Services\Blocks;
use Ramona\CMS\Admin\UI\LayoutRenderer;
use Ramsey\Uuid\Uuid;

final class GetEditBlock implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'blocks.edit';

    public function __construct(
        private readonly LayoutRenderer $layoutRenderer,
        private readonly Blocks $blocks,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        assert($id === null || is_string($id));

        $initialContent = '';

        if ($id !== null) {
            $block = $this->blocks->find(Uuid::fromString($id));

            $initialContent = $block->content();
        }

        $editorView = new EditorView($initialContent);

        return $this->layoutRenderer->render($editorView, $request);
    }
}
