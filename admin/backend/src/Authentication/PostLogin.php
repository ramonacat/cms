<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use FastRoute\GenerateUri;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\LayoutView;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\Form;
use RuntimeException;

final class PostLogin implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private \Ramona\CMS\Admin\Authentication\Services\User $userService,
        private GenerateUri $uriGenerator,
        private FrontendModuleLoader $frontendModuleLoader,
        private TemplateFactory $templateFactory,
        private StreamFactoryInterface $streamFactory
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
            return $this
                ->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $this->uriGenerator->forRoute(Home::ROUTE_NAME));
        }

        $frontendModule = $this->frontendModuleLoader->load('login');
        $loginTemplate = $this->templateFactory->create(
            'user/login.php',
            new LoginView(
                $frontendModule->cssModule ?? throw new RuntimeException('Missing login CSS module'),
                new Form(['Incorrect username or password'])
            )
        );
        $layoutView = $this->templateFactory->create(
            'layout.php',
            new LayoutView(
                $loginTemplate,
                [$frontendModule]
            )
        );

        return $this
            ->responseFactory
            ->createResponse(200, 'OK')
            ->withBody($this->streamFactory->createStream($layoutView->render()));

    }
}
