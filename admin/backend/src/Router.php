<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Closure;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Router
{
    /**
     * @param Closure(\FastRoute\RouteCollector):void $registerRoutes
     */
    public function __construct(
        private ContainerInterface $container,
        private Closure $registerRoutes
    ) {
    }

    public function route(ServerRequestInterface $request): ResponseInterface
    {
        $router = \FastRoute\simpleDispatcher($this->registerRoutes);
        $result = $router->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($result[0]) {
            case Dispatcher::METHOD_NOT_ALLOWED:
                /** @var array{int, list<string>} $result */
                $handler = new MethodNotAllowedHandler($result[1]);
                break;

            case Dispatcher::FOUND:
                assert(is_string($result[1]) && class_exists($result[1]));
                /** @var array{int, class-string, array<string, string>} $result */
                $handler = $this->container->get($result[1]);

                foreach ($result[2] as $variableName => $variableValue) {
                    $request = $request->withAttribute($variableName, $variableValue);
                }

                break;

            case Dispatcher::NOT_FOUND:
            default:
                $handler = new NotFoundHandler();
                break;
        }

        assert($handler instanceof RequestHandlerInterface);
        return $handler->handle($request);
    }
}
