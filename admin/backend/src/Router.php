<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Router implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $container,
        private \FastRoute\Dispatcher $dispatcher,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $nextHandler): ResponseInterface
    {
        $result = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($result[0]) {
            case Dispatcher::METHOD_NOT_ALLOWED:
                /** @var list<string> $allowedMethods */
                $allowedMethods = $result[1];
                $handler = new MethodNotAllowedHandler($allowedMethods);
                break;

            case Dispatcher::FOUND:
                assert(is_string($result[1]) && class_exists($result[1]) && is_iterable($result[2]));
                $handler = $this->container->get($result[1]);

                foreach ($result[2] as $variableName => $variableValue) {
                    $request = $request->withAttribute($variableName, $variableValue);
                }

                break;

            case Dispatcher::NOT_FOUND:
            default:
                $handler = $nextHandler;
                break;
        }

        assert($handler instanceof RequestHandlerInterface);

        return $handler->handle($request);
    }
}
