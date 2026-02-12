<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260212000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update organoleptics date to datetime';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organolepticas ALTER COLUMN fecha TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organolepticas ALTER COLUMN fecha TYPE DATE');
    }
}
