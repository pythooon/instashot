<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251227100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store Phoenix API access token on user (profile import).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD phoenix_access_token TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP phoenix_access_token');
    }
}
