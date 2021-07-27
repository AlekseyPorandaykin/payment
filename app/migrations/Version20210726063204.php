<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210726063204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавить основную и популярные валюты';
    }

    public function up(Schema $schema): void
    {

        $this->addSql("
            INSERT IGNORE INTO currencies(code, name, created_at)
            VALUES ('USD', 'Доллар США', CURRENT_TIMESTAMP()),
                   ('EUR', 'Евро', CURRENT_TIMESTAMP),
                   ('GBR', 'Фунт стерлингов', CURRENT_TIMESTAMP()),
                   ('RUB', 'Российский рубль', CURRENT_TIMESTAMP);
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM currencies WHERE code in ('USD', 'EUR', 'GBR', 'RUB')");
    }
}
