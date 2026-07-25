<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** Creates the expense table. */
final class Version20260725144704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the expense table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE expense (id BLOB NOT NULL, description VARCHAR(255) NOT NULL, amount_in_cents INTEGER NOT NULL, spent_on DATE NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE expense');
    }
}
