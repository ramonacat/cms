<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250222115944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE blocks (
                id UUID PRIMARY KEY,
                content TEXT NOT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
    }
}
