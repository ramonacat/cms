<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use FastRoute\GenerateUri;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramona\CMS\Admin\Authentication\GetLogin;
use Ramona\CMS\Admin\Authentication\Services\User;

final class Home implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'home';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private GenerateUri $urlGenerator,
        private User $userService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        assert($session instanceof SessionInterface);

        $user = $this->userService->currentlyLoggedIn($session);

        if ($user === null) {
            return $this
                ->responseFactory
                ->createResponse(code: 302)
                ->withHeader('Location', $this->urlGenerator->forRoute(GetLogin::ROUTE_NAME));
        }
        $response = $this
            ->responseFactory
            ->createResponse(302);

        // TODO: Render a template here with the admin homepage
        $response->getBody()->write('Logged in as: ' . $user->username());

        return $response;

    }
}
