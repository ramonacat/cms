<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MethodNotAllowedHandler implements RequestHandlerInterface
{
    /**
     * @param list<string> $allowedMethods
     */
    public function __construct(
        private array $allowedMethods
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response(status: 405);
        $response->getBody()->write('Method not allowed. Allowed methods: ' . implode(', ', $this->allowedMethods));

        return $response;
    }
}
