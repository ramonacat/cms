<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication\Services;

use Ramsey\Uuid\UuidInterface;

final class LoggedInUser
{
    public function __construct(
        private UuidInterface $id,
        private string $username
    ) {
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }
}
