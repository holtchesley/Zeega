<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120209174241 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Item ADD archive VARCHAR(50) NOT NULL");
		$this->addSql("ALTER TABLE Item CHANGE type media_type VARCHAR(20) NOT NULL");
		$this->addSql("ALTER TABLE Item CHANGE source layer_type VARCHAR(20) NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Item ADD type VARCHAR(20) p NULL, ADD source VARCHAR(20) NOT NULL, DROP archive, DROP media_type, DROP layer_type");
    }
}
