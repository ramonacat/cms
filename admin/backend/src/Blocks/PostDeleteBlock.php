<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use FastRoute\GenerateUri;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramona\CMS\Admin\Blocks\Services\Blocks;
use Ramsey\Uuid\Uuid;

final class PostDeleteBlock implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'blocks.delete.post';

    public function __construct(
        private readonly Blocks $blocks,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly GenerateUri $uriGenerator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        assert(is_string($id));

        $id = Uuid::fromString($id);

        $this->blocks->delete($id);

        return $this
            ->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $this->uriGenerator->forRoute(GetBlocks::ROUTE_NAME));
    }
}
