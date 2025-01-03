<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Home implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private \FastRoute\GenerateUri $urlGenerator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this
            ->responseFactory
            ->createResponse(code: 302)
            ->withHeader('Location', $this->urlGenerator->forRoute('user.login'));
    }
}
