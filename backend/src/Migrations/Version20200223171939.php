<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200223171939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO `user` (`username`, `created_at`) VALUES (\'test\',\'2002-01-20 17:35:26\');');
        $this->addSql('INSERT INTO `user` (`username`, `created_at`) VALUES (\'user\',\'2010-06-13 12:51:21\');');
        $this->addSql('INSERT INTO `user` (`username`, `created_at`) VALUES (\'qwerty\',\'2012-12-07 20:43:15\');');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
    }
}
