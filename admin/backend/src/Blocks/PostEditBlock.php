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

final class PostEditBlock implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'blocks.edit.post';

    public function __construct(
        private readonly Blocks $blocks,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly GenerateUri $uriGenerator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $requestBody = $request->getParsedBody();
        assert(is_array($requestBody));
        assert($id === null || is_string($id));

        if ($id === null) {
            $this->blocks->create((string) $requestBody['content']);
        } else {
            $id = Uuid::fromString($id);

            $this->blocks->update($id, (string) $requestBody['content']);
        }

        return $this->responseFactory->createResponse(302)->withHeader('Location', $this->uriGenerator->forRoute(GetBlocks::ROUTE_NAME));
    }
}
