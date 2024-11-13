<?php

namespace Tests\Ramona\CMS\Admin;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class HandlerMock implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response(200);
        $response->getBody()->write(\Safe\json_encode($request->getAttributes()));
        $response->getBody()->seek(0);

        return $response;
    }
}
