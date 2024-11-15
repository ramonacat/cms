<?php

declare(strict_types=1);

use FastRoute\ConfigureRoutes;
use FastRoute\GenerateUri;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\Login;
use Ramona\CMS\Admin\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$psr17factory = new Psr17Factory();

$fastRoute = \FastRoute\FastRoute::recommendedSettings(
    function (ConfigureRoutes $r) {
        $r->get('/', Home::class, [
            ConfigureRoutes::ROUTE_NAME => 'home',
        ]);
        $r->get('/login', Login::class, [
            ConfigureRoutes::ROUTE_NAME => 'user.login',
        ]);
    },
    sys_get_temp_dir() . '/cms-routes'
);

$container = new \DI\Container([
    ResponseFactoryInterface::class => \DI\get(Psr17Factory::class),
    StreamFactoryInterface::class => \DI\get(Psr17Factory::class),
    GenerateUri::class => $fastRoute->uriGenerator(),
]);

$requestCreator = new ServerRequestCreator(
    $psr17factory,
    $psr17factory,
    $psr17factory,
    $psr17factory
);
$request = $requestCreator->fromGlobals();

$router = new Router($container, $fastRoute->dispatcher());

$response = $router->route($request);

(new SapiEmitter())->emit($response);
