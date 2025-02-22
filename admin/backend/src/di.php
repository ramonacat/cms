<?php

declare(strict_types=1);

use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;
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
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramona\CMS\Admin\Authentication\GetLogin;
use Ramona\CMS\Admin\Authentication\PostLogin;
use Ramona\CMS\Admin\Authentication\Services\User;
use Ramona\CMS\Admin\Blocks\GetBlocks;
use Ramona\CMS\Admin\Blocks\GetEditBlock;
use Ramona\CMS\Admin\Blocks\PostDeleteBlock;
use Ramona\CMS\Admin\Blocks\PostEditBlock;
use Ramona\CMS\Admin\Frontend\CSSModuleLoader;
use Ramona\CMS\Admin\Frontend\FrontendModuleLoader;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\LayoutView;
use Ramona\CMS\Admin\Router;
use Ramona\CMS\Admin\TemplateFactory;
use Ramona\CMS\Admin\UI\LayoutRenderer;
use Ramona\CMS\Admin\UI\ViewRenderer;
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

        $r->get('/blocks', GetBlocks::class, [
            ConfigureRoutes::ROUTE_NAME => GetBlocks::ROUTE_NAME,
        ]);
        $r->get('/blocks/edit[/{id}]', GetEditBlock::class, [
            ConfigureRoutes::ROUTE_NAME => GetEditBlock::ROUTE_NAME,
        ]);
        $r->post('/blocks/edit[/{id}]', PostEditBlock::class, [
            ConfigureRoutes::ROUTE_NAME => PostEditBlock::ROUTE_NAME,
        ]);
        $r->post('/blocks/delete[/{id}]', PostDeleteBlock::class, [
            ConfigureRoutes::ROUTE_NAME => PostDeleteBlock::ROUTE_NAME,
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
    LayoutRenderer::class => function (ContainerInterface $container) {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        /** @var ViewRenderer $viewRenderer */
        $viewRenderer = $container->get(ViewRenderer::class);
        return new LayoutRenderer(
            static function ($child, $request) use ($container) {
                /** @var ViewRenderer $viewRenderer */
                $viewRenderer = $container->get(ViewRenderer::class);
                /** @var User $userService */
                $userService = $container->get(User::class);
                /** @var GenerateUri $uriGenerator */
                $uriGenerator = $container->get(GenerateUri::class);
                $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
                assert($session instanceof SessionInterface);

                $user = $userService->currentlyLoggedIn($session);
                assert($user !== null);

                return new LayoutView(
                    $viewRenderer->render($child),
                    $child->requiredFrontendModules(),
                    $user,
                    $uriGenerator,
                );
            },
            $responseFactory,
            $viewRenderer
        );
    },
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
            (new Configuration(\Lcobucci\JWT\Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::base64Encoded('oCYq82M/naO5GaVPUBYgKtJMz9TkFRhv4hDEN0Hq4xE=') // TODO: for prod this must be an actual secure key
            )))
            // TODO: use default cookie settings for prod!
                ->withCookie(SetCookie::create('slsession')->withHttpOnly(true)->withSameSite(SameSite::lax())->withPath('/'))
        ));
        $pipe->pipe($router);

        return $pipe;
    },
]);

return $container;
