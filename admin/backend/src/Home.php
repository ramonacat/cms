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
use Ramona\CMS\Admin\UI\LayoutRenderer;

final class Home implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'home';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private GenerateUri $uriGenerator,
        private User $userService,
        private LayoutRenderer $layoutRenderer,
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
                ->withHeader('Location', $this->uriGenerator->forRoute(GetLogin::ROUTE_NAME));
        }

        $homeView = new HomeView();

        return $this->layoutRenderer->render($homeView, $request);
    }
}
