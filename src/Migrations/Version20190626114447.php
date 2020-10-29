<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190626114447 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS brand (id INT AUTO_INCREMENT NOT NULL, brand_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS model (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, model_name VARCHAR(255) NOT NULL, INDEX IDX_D79572D944F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS car_ad (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, user_id INT DEFAULT NULL, horse_power INT NOT NULL, miliage INT NOT NULL, colour VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_B1F7C9744F5D008 (brand_id), INDEX IDX_B1F7C97A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', jwt VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), INDEX user_email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE model ADD CONSTRAINT FK_D79572D944F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE car_ad ADD CONSTRAINT FK_B1F7C9744F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE car_ad ADD CONSTRAINT FK_B1F7C97A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql("INSERT brand (id, brand_name) VALUES (1, 'BMW'), (2, 'Ferrari'), (3, 'Audi'), (4, 'Lamborghini')");
        $this->addSql("INSERT model (id, brand_id, model_name) VALUES (1, 1, 'M6'), (2, 1, 'X6'), (3, 1, '550'), (4, 2, 'Enzo'), (5, 2, 'F50'), (6, 2, 'LaFerrari'), (7, 3, 'RS8'), (8, 3, 'A8'), (9, 3, 'TT'), (10, 4, 'Diablo'), (11, 4, 'Aventador'), (12, 4, 'Huracane')");
        $this->addSql('DROP TABLE IF EXISTS sample_table');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE model DROP FOREIGN KEY FK_D79572D944F5D008');
        $this->addSql('ALTER TABLE car_ad DROP FOREIGN KEY FK_B1F7C9744F5D008');
        $this->addSql('ALTER TABLE car_ad DROP FOREIGN KEY FK_B1F7C97A76ED395');
        $this->addSql('CREATE TABLE IF NOT EXISTS sample_table (sample_table_id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(sample_table_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE IF EXISTS brand');
        $this->addSql('DROP TABLE IF EXISTS model');
        $this->addSql('DROP TABLE IF EXISTS car_ad');
        $this->addSql('DROP TABLE IF EXISTS user');
    }
}
