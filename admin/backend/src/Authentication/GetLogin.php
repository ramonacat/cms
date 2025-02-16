<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramona\CMS\Admin\LoginLayoutView;
use Ramona\CMS\Admin\UI\Form;
use Ramona\CMS\Admin\UI\ViewRenderer;

final class GetLogin implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'authentication.login';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private ViewRenderer $viewRenderer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $loginTemplate = $this->viewRenderer->render(new LoginView(new Form([])));
        $layoutTemplate = $this->viewRenderer->render(new LoginLayoutView($loginTemplate, ['login']));

        return $this
            ->responseFactory
            ->createResponse(200, 'OK')
            ->withBody($this->streamFactory->createStream($layoutTemplate->render()));
    }
}
