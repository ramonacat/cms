<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101174920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id UUID PRIMARY KEY,
            username TEXT NOT NULL,
            password_hash TEXT NOT NULL
        )');
    }
}
