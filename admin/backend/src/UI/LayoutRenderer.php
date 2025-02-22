<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\UI;

use Closure;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LayoutRenderer
{
    /**
     * @param Closure(ChildView,ServerRequestInterface):View $createLayoutView
     */
    public function __construct(
        private readonly Closure $createLayoutView,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ViewRenderer $viewRenderer,
    ) {
    }

    public function render(ChildView $view, ServerRequestInterface $request): ResponseInterface
    {
        $layoutView = ($this->createLayoutView)($view, $request);

        $response = $this->responseFactory->createResponse(200);
        $response->getBody()->write($this->viewRenderer->render($layoutView)->render());

        return $response;
    }
}
