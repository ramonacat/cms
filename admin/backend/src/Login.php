<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class Login implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private TemplateFactory $templateFactory,
        private FrontendModuleLoader $frontendModuleLoader,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $frontendModule = $this->frontendModuleLoader->load('login');
        $loginTemplate = $this->templateFactory->create(
            'user/login.php',
            new LoginView($frontendModule->cssModule ?? throw new RuntimeException('Missing login CSS module'))
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
