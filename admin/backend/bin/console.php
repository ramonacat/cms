<?php

use Ramona\CMS\Admin\Authentication\Commands\CreateAccount;
use Symfony\Component\Console\Application;

require_once __DIR__.'/../vendor/autoload.php';

$container = require __DIR__.'/../src/di.php';

$application = new Application();
$application->add(new CreateAccount($container));
$application->run();
