<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\LayoutView;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\Form;
use RuntimeException;

final class GetLogin implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'authentication.login';

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
            new LoginView(
                $frontendModule->cssModule ?? throw new RuntimeException('Missing login CSS module'),
                new Form([])
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
