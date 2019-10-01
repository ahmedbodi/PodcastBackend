<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191001191827 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE episode ADD track_length INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD bit_rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD sample_rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD channels INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD is_variable_bit_rate BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD is_lossless BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE episode DROP track_length');
        $this->addSql('ALTER TABLE episode DROP bit_rate');
        $this->addSql('ALTER TABLE episode DROP sample_rate');
        $this->addSql('ALTER TABLE episode DROP channels');
        $this->addSql('ALTER TABLE episode DROP is_variable_bit_rate');
        $this->addSql('ALTER TABLE episode DROP is_lossless');
    }
}
