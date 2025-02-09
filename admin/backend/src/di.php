<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use FastRoute\ConfigureRoutes;
use FastRoute\Dispatcher;
use FastRoute\GenerateUri;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use PSR7Sessions\Storageless\Http\Configuration;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Ramona\CMS\Admin\Authentication\GetLogin;
use Ramona\CMS\Admin\Authentication\PostLogin;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\Router;
use Ramona\CMS\Admin\TemplateFactory;
use Ramsey\Uuid\Doctrine\UuidType;

$fastRoute = \FastRoute\FastRoute::recommendedSettings(
    function (ConfigureRoutes $r) {
        $r->get('/', Home::class, [
            ConfigureRoutes::ROUTE_NAME => Home::ROUTE_NAME,
        ]);
        $r->get('/login', GetLogin::class, [
            ConfigureRoutes::ROUTE_NAME => GetLogin::ROUTE_NAME,
        ]);
        $r->post('/login', PostLogin::class, [
            ConfigureRoutes::ROUTE_NAME => 'user.login.post',
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
            'https://localhost:5173/', // todo this URL makes sense only in DEV
            __DIR__ . '/../../frontend/dist/.vite/manifest.json',
            $cssModuleLoader
        );
    },
    EntityManagerInterface::class => function () {
        \Doctrine\DBAL\Types\Type::addType('uuid', UuidType::class);

        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__], isDevMode: true); // TODO: also support non-dev mode
        $connection = DriverManager::getConnection(require __DIR__ . '/../migrations-db.php', $config); // TODO: in prod mode there's potentially separate connections for migrations and for the app itself

        return new EntityManager($connection, $config);
    },
    \Laminas\Stratigility\MiddlewarePipe::class => function (ContainerInterface $container) {
        /** @var Router $router */
        $router = $container->get(Router::class);
        $pipe = new \Laminas\Stratigility\MiddlewarePipe();
        $pipe->pipe(new SessionMiddleware(
            new Configuration(\Lcobucci\JWT\Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::base64Encoded('oCYq82M/naO5GaVPUBYgKtJMz9TkFRhv4hDEN0Hq4xE=') // TODO: for prod this must be an actual secure key
            ))
        ));
        $pipe->pipe($router);

        return $pipe;
    },
]);

return $container;
