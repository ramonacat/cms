<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use FastRoute\ConfigureRoutes;
use FastRoute\Dispatcher;
use FastRoute\GenerateUri;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Ramona\CMS\Admin\Authentication\GetLogin;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\TemplateFactory;

$fastRoute = \FastRoute\FastRoute::recommendedSettings(
    function (ConfigureRoutes $r) {
        $r->get('/', Home::class, [
            ConfigureRoutes::ROUTE_NAME => 'home',
        ]);
        $r->get('/login', GetLogin::class, [
            ConfigureRoutes::ROUTE_NAME => 'user.login',
        ]);
    },
    sys_get_temp_dir() . '/cms-routes'
);

$container = new \DI\Container([
    ResponseFactoryInterface::class => \DI\get(Psr17Factory::class),
    StreamFactoryInterface::class => \DI\get(Psr17Factory::class),
    GenerateUri::class => $fastRoute->uriGenerator(),
    Dispatcher::class => $fastRoute->dispatcher(),
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
    EntityManagerInterface::class => function () {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__], isDevMode: true); // TODO: also support non-dev mode
        $connection = DriverManager::getConnection(require __DIR__ . '/../migrations-db.php', $config); // TODO: in prod mode there's potentially separate connections for migrations and for the app itself

        return new EntityManager($connection, $config);
    },
]);

return $container;
