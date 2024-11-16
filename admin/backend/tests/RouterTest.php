<?php

namespace Tests\Ramona\CMS\Admin;

use FastRoute\ConfigureRoutes;
use FastRoute\FastRoute;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Ramona\CMS\Admin\Home;
use Ramona\CMS\Admin\Router;

final class RouterTest extends TestCase {
    private MockObject&ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testReturnsMethodNotAllowedHandlerForInvalidMethod(): void
    {
        $dispatcher = FastRoute::recommendedSettings(function (ConfigureRoutes $r) {
            $r->get('/', Home::class);
        }, __FUNCTION__)->disableCache()->dispatcher();
        $router = new Router($this->container, $dispatcher);
        $result = $router->route(new ServerRequest('POST', 'https://localhost:8080/'));

        self::assertEquals(405, $result->getStatusCode());
    }

    public function testReturnsNotFoundHandlerForNotFound(): void
    {
        $dispatcher = FastRoute::recommendedSettings(function (ConfigureRoutes $r) {
            $r->get('/', Home::class);
        }, __FUNCTION__)->disableCache()->dispatcher();
        $router = new Router($this->container, $dispatcher);
        $result = $router->route(
            new ServerRequest('GET', 'https://localhost:8080/rainbows')
        );

        self::assertEquals(404, $result->getStatusCode());
    }

    public function testCanRouteAMatchingHandler(): void
    {
        $handler = new HandlerMock();
        $this->container->method('get')->willReturn($handler);
        $dispatcher = FastRoute::recommendedSettings(function (ConfigureRoutes $r) {
            $r->get('/rainbows/{a}/{b}', HandlerMock::class);
        }, __FUNCTION__)->disableCache()->dispatcher();

        $router = new Router($this->container, $dispatcher);
        $request = new ServerRequest('GET', 'https://localhost:8080/rainbows/111/222');
        $result = $router->route($request);

        self::assertJsonStringEqualsJsonString(
            '{"a": "111", "b": "222"}', 
            $result->getBody()->read(4096)
        );
    }   
}
