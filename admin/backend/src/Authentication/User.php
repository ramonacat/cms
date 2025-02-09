<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table('users')]
final class User
{
    #[ORM\Id]
    #[ORM\Column('id', 'uuid')]
    private readonly UuidInterface $id;

    #[ORM\Column('username', 'string')]
    private string $username;

    #[ORM\Column('password_hash', 'text')]
    private string $passwordHash;

    /**
     * @param callable(string):string $hashPassword
     */
    private function __construct(UuidInterface $id, string $username, string $password, callable $hashPassword)
    {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $hashPassword($password);
    }

    /**
     * @param callable(string):string $hashPassword
     */
    public static function create(UuidInterface $id, string $username, string $password, callable $hashPassword): self
    {
        return new self($id, $username, $password, $hashPassword);
    }

    /**
     * @param callable(string, string):bool $check
     */
    public function checkPassword(string $password, callable $check): bool
    {
        return $check($password, $this->passwordHash);
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
}
