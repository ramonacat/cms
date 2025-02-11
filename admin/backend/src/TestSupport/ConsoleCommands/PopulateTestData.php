<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\TestSupport\ConsoleCommands;

use Psr\Container\ContainerInterface;
use Ramona\CMS\Admin\Authentication\Services\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test-support:populate-test-data')]
final class PopulateTestData extends Command
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var User $userService */
        $userService = $this->container->get(User::class);

        $userService->createAccount('testuser', 'testpwd');

        return self::SUCCESS;
    }
}
