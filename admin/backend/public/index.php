<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\MethodNotAllowedHandler;
use Ramona\CMS\Admin\NotFoundHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$psr17factory = new Psr17Factory();

$container = new \DI\Container([
    ResponseFactoryInterface::class => \DI\get(Psr17Factory::class),
    StreamFactoryInterface::class => \DI\get(Psr17Factory::class),
]);

$requestCreator = new ServerRequestCreator($psr17factory, $psr17factory, $psr17factory, $psr17factory);
$request = $requestCreator->fromGlobals();

$router = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
    $r->get('/', Home::class);
});
$result = $router->dispatch($request->getMethod(), $request->getUri()->getPath());
switch ($result[0]) {
    case Dispatcher::METHOD_NOT_ALLOWED:
        $handler = new MethodNotAllowedHandler($result[1]);
        break;

    case Dispatcher::FOUND:
        $handler = $container->get($result[1]);
        foreach ($result[2] as $variableName => $variableValue) {
            $request = $request->withAttribute($variableName, $variableValue);
        }
        
        break;

    case Dispatcher::NOT_FOUND:
    default:
        $handler = new NotFoundHandler();
        break;
}

$response = $handler->handle($request);
(new SapiEmitter())->emit($response);
