<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180724222849 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exams CHANGE exam_status exam_status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE users ADD is_active TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E935C246D5 ON users (password)');
        $this->addSql('ALTER TABLE validations CHANGE valid_status valid_status SMALLINT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exams CHANGE exam_status exam_status TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E935C246D5 ON users');
        $this->addSql('ALTER TABLE users DROP is_active');
        $this->addSql('ALTER TABLE validations CHANGE valid_status valid_status TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
