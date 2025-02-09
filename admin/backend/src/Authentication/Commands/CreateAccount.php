<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication\Commands;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Ramona\CMS\Admin\Authentication\PasswordHasher;
use Ramona\CMS\Admin\Authentication\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'user:create')]
final class CreateAccount extends Command
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        assert(is_string($username));
        assert(is_string($password));

        $user = User::create(Uuid::uuid7(), $username, $password, [PasswordHasher::class, 'hash']);

        $em->persist($user);
        $em->flush();

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }
}
