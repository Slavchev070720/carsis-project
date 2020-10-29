<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190701073046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_ad ADD model_id INT NOT NULL, CHANGE brand_id brand_id INT NOT NULL');
        $this->addSql('ALTER TABLE car_ad ADD CONSTRAINT FK_B1F7C977975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('CREATE INDEX IDX_B1F7C977975B7E7 ON car_ad (model_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_ad DROP FOREIGN KEY FK_B1F7C977975B7E7');
        $this->addSql('DROP INDEX IDX_B1F7C977975B7E7 ON car_ad');
        $this->addSql('ALTER TABLE car_ad DROP model_id, CHANGE brand_id brand_id INT DEFAULT NULL');
    }
}
