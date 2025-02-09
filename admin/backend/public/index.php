<?php

declare(strict_types=1);

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/../src/di.php';

$psr17factory = new Psr17Factory();

$requestCreator = new ServerRequestCreator(
    $psr17factory,
    $psr17factory,
    $psr17factory,
    $psr17factory
);
$request = $requestCreator->fromGlobals();

$runner = new RequestHandlerRunner(
    $container->get(\Laminas\Stratigility\MiddlewarePipe::class),
    new SapiEmitter(),
    fn () => $request,
    static function (\Throwable $e) use ($psr17factory) {
        return $psr17factory->createResponse(500, 'Internal Server Error');
    }
);
$runner->run();
