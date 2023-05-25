<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525122108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE node (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', data JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE node_file (node_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', file_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4B3BBB86460D9FD7 (node_id), INDEX IDX_4B3BBB8693CB796C (file_id), PRIMARY KEY(node_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE node_file ADD CONSTRAINT FK_4B3BBB86460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE node_file ADD CONSTRAINT FK_4B3BBB8693CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE node_file DROP FOREIGN KEY FK_4B3BBB86460D9FD7');
        $this->addSql('ALTER TABLE node_file DROP FOREIGN KEY FK_4B3BBB8693CB796C');
        $this->addSql('DROP TABLE node');
        $this->addSql('DROP TABLE node_file');
    }
}
