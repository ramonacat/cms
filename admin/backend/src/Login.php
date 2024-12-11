<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Login implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private TemplateFactory $templateFactory,
        private CSSModuleLoader $cssModuleLoader,
        private FrontendModuleLoader $frontendModuleLoader,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $loginTemplate = $this->templateFactory->create(
            'user/login.php',
            new LoginView($this->cssModuleLoader->load('login'))
        );
        $layoutView = $this->templateFactory->create(
            'layout.php',
            new LayoutView(
                $loginTemplate,
                [$this->frontendModuleLoader->load('login')]
            )
        );

        return $this
            ->responseFactory
            ->createResponse(200, 'OK')
            ->withBody($this->streamFactory->createStream($layoutView->render()));
    }
}
