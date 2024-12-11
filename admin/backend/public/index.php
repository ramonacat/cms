<?php

declare(strict_types=1);

use FastRoute\ConfigureRoutes;
use FastRoute\GenerateUri;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ramona\CMS\Admin\CSSModuleLoader;
use Ramona\CMS\Admin\FrontendModuleLoader;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\Login;
use Ramona\CMS\Admin\Router;
use Ramona\CMS\Admin\TemplateFactory;

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
    TemplateFactory::class => fn () => new TemplateFactory(__DIR__ . '/../src/templates/'),
    CSSModuleLoader::class => fn () => new CSSModuleLoader(__DIR__ . '/../../frontend/dist-server/css-modules/'),
    FrontendModuleLoader::class => function (ContainerInterface $c) {
        $cssModuleLoader = $c->get(CSSModuleLoader::class);
        /** @var CSSModuleLoader $cssModuleLoader */
        return new FrontendModuleLoader(
            'https://localhost:5173/',
            __DIR__ . '/../../frontend/dist/.vite/manifest.json',
            $cssModuleLoader
        );
    },
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
