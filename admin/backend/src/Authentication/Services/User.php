<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication\Services;

use Doctrine\ORM\EntityManagerInterface;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Ramona\CMS\Admin\Authentication\PasswordHasher;
use Ramona\CMS\Admin\Authentication\User as UserEntity;
use Ramsey\Uuid\Uuid;

final class User
{
    private const SESSION_USER_ID = 'user.id';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createAccount(string $username, string $password): void
    {
        $user = UserEntity::create(Uuid::uuid7(), $username, $password, [PasswordHasher::class, 'hash']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }

    public function login(SessionInterface $session, string $username, string $password): bool
    {
        $user = $this->entityManager->getRepository(UserEntity::class)->findOneBy([
            'username' => $username,
        ]);

        if ($user === null) {
            return false;
        }

        $result = $user->checkPassword($password, [PasswordHasher::class, 'verify']);
        if ($result) {
            $session->set(self::SESSION_USER_ID, $user->id()->toString());
        }

        return $result;
    }

    public function currentlyLoggedIn(SessionInterface $session): ?LoggedInUser
    {
        $userId = $session->get(self::SESSION_USER_ID);

        if ($userId === null) {
            return null;
        }

        $user = $this->entityManager->find(UserEntity::class, $userId);

        /// TODO: This means the account no longer exist, should we remove the logged in session?
        if ($user === null) {
            return null;
        }

        return new LoggedInUser($user->id(), $user->username());
    }
}
