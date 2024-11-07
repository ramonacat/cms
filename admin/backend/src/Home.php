<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Home implements RequestHandlerInterface
{
    public function __construct(
        private StreamFactoryInterface $streamFactory,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $this->streamFactory->createStream('Hello');
        $response = $this->responseFactory->createResponse(code: 200, reasonPhrase: 'OK')->withBody($body);

        return $response;
    }
}
