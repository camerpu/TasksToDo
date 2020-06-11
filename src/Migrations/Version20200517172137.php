<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517172137 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE description description VARCHAR(2000) DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE added_by_id added_by_id INT DEFAULT NULL, CHANGE taken_by_id taken_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE primary_clan_id primary_clan_id BIGINT DEFAULT NULL, CHANGE join_date join_date DATETIME DEFAULT NULL, CHANGE country_code country_code VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP created_at, DROP updated_at, CHANGE description description VARCHAR(2000) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE task CHANGE added_by_id added_by_id INT DEFAULT NULL, CHANGE taken_by_id taken_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE primary_clan_id primary_clan_id BIGINT DEFAULT NULL, CHANGE join_date join_date DATETIME DEFAULT \'NULL\', CHANGE country_code country_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
