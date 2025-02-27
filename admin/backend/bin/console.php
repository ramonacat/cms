<?php

declare(strict_types=1);

use Ramona\CMS\Admin\Authentication\ConsoleCommands\CreateAccount;
use Ramona\CMS\Admin\TestSupport\ConsoleCommands\PopulateTestData;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../src/di.php';

$application = new Application();
$application->add(new CreateAccount($container));
$application->add(new PopulateTestData($container));
$application->run();
