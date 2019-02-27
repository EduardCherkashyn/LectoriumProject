<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227064722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE homework (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, home_task_id INT NOT NULL, git_hub_repository VARCHAR(255) NOT NULL, status INT NOT NULL, INDEX IDX_8C600B4ECB944F1A (student_id), INDEX IDX_8C600B4E3896CADE (home_task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_base_class (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F7074EA1E7927C74 (email), UNIQUE INDEX UNIQ_F7074EA17BA2F5EB (api_token), INDEX IDX_F7074EA1591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_task (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, date DATETIME NOT NULL, status INT NOT NULL, description LONGTEXT NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_73586AEA1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, home_work_id INT NOT NULL, name VARCHAR(255) NOT NULL, status INT NOT NULL, INDEX IDX_527EDB25E149883 (home_work_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, year DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, description LONGTEXT NOT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_7CC7DA2C1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, plan_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status INT DEFAULT NULL, INDEX IDX_9D40DE1BE899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, text LONGTEXT NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_DD5A5B7D591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE homework ADD CONSTRAINT FK_8C600B4ECB944F1A FOREIGN KEY (student_id) REFERENCES user_base_class (id)');
        $this->addSql('ALTER TABLE homework ADD CONSTRAINT FK_8C600B4E3896CADE FOREIGN KEY (home_task_id) REFERENCES home_task (id)');
        $this->addSql('ALTER TABLE user_base_class ADD CONSTRAINT FK_F7074EA1591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE home_task ADD CONSTRAINT FK_73586AEA1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25E149883 FOREIGN KEY (home_work_id) REFERENCES homework (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BE899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user_base_class (id)');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25E149883');
        $this->addSql('ALTER TABLE homework DROP FOREIGN KEY FK_8C600B4ECB944F1A');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE homework DROP FOREIGN KEY FK_8C600B4E3896CADE');
        $this->addSql('ALTER TABLE user_base_class DROP FOREIGN KEY FK_F7074EA1591CC992');
        $this->addSql('ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7D591CC992');
        $this->addSql('ALTER TABLE home_task DROP FOREIGN KEY FK_73586AEA1F55203D');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C1F55203D');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BE899029B');
        $this->addSql('DROP TABLE homework');
        $this->addSql('DROP TABLE user_base_class');
        $this->addSql('DROP TABLE home_task');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE plan');
    }
}
