<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Ramona\CMS\Admin\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$psr17factory = new Psr17Factory();

$container = require_once __DIR__ . '/../src/di.php';

$requestCreator = new ServerRequestCreator(
    $psr17factory,
    $psr17factory,
    $psr17factory,
    $psr17factory
);
$request = $requestCreator->fromGlobals();

$router = new Router($container, $container->get(Dispatcher::class));

$response = $router->route($request);

(new SapiEmitter())->emit($response);
