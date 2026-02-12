<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260212000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add compatibility and stage restriction fields to productos';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE productos ADD compatibilidades JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE productos ADD incompatibilidades JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE productos ADD restricciones_etapas JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE productos DROP compatibilidades');
        $this->addSql('ALTER TABLE productos DROP incompatibilidades');
        $this->addSql('ALTER TABLE productos DROP restricciones_etapas');
    }
}
