<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use FastRoute\GenerateUri;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramona\CMS\Admin\Home;

final class PostLogin implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private \Ramona\CMS\Admin\Authentication\Services\User $userService,
        private GenerateUri $uriGenerator,
    ) {

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        if (! is_array($parsedBody)) {
            return $this->responseFactory->createResponse(400, 'Bad Request');
        }

        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        assert($session instanceof SessionInterface);

        $authResult = $this
            ->userService
            ->login(
                $session,
                $parsedBody['username'],
                $parsedBody['password']
            );

        if ($authResult) {
            $response = $this->responseFactory->createResponse(302)->withHeader('Location', $this->uriGenerator->forRoute(Home::ROUTE_NAME));

            return $response;
        }

        $response = $this
            ->responseFactory
            ->createResponse(200, 'OK');
        $response->getBody()->write('Incorrect username/password'); // todo redirect to GetLogin, with an error message
        return $response;

    }
}
