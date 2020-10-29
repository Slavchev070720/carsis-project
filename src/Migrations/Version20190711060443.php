<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190711060443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX brand_idx ON brand (brand_name)');
        $this->addSql('CREATE INDEX model_idx ON model (model_name)');
        $this->addSql('CREATE INDEX price_idx ON car_ad (price)');
        $this->addSql('DROP INDEX user_email_idx ON user');
        $this->addSql('CREATE INDEX username_idx ON user (username)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX brand_idx ON brand');
        $this->addSql('DROP INDEX price_idx ON car_ad');
        $this->addSql('DROP INDEX model_idx ON model');
        $this->addSql('DROP INDEX username_idx ON user');
        $this->addSql('CREATE INDEX user_email_idx ON user (email)');
    }
}
