<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115214325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delegation (id CHAR(36) NOT NULL --(DC2Type:guid)
        , name CLOB NOT NULL, registration_hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, contestant_count INTEGER NOT NULL, leader_count INTEGER NOT NULL, guest_count INTEGER NOT NULL, participation_mode INTEGER NOT NULL, attendance_review_progress INTEGER NOT NULL, translations CLOB DEFAULT NULL --(DC2Type:simple_array)
        , contribution_review_progress INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_292F436D5E237E06 ON delegation (name)');
        $this->addSql('CREATE TABLE email (id CHAR(36) NOT NULL --(DC2Type:guid)
        , sent_by_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , identifier CHAR(36) NOT NULL --(DC2Type:guid)
        , type INTEGER NOT NULL, sent_date_time DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E7927C74A45BB98C ON email (sent_by_id)');
        $this->addSql('CREATE TABLE participant (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , arrival_travel_group_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , departure_travel_group_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , role INTEGER NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, given_name CLOB DEFAULT NULL, family_name CLOB DEFAULT NULL, birthday DATETIME DEFAULT NULL, email CLOB DEFAULT NULL, gender CLOB DEFAULT NULL, name_on_documents CLOB DEFAULT NULL, portrait CLOB DEFAULT NULL, papers CLOB DEFAULT NULL, consent CLOB DEFAULT NULL, personal_data_review_progress INTEGER NOT NULL, nationality CLOB DEFAULT NULL, passport_number CLOB DEFAULT NULL, passport_validity_from DATE DEFAULT NULL, passport_validity_to DATE DEFAULT NULL, passport_issue_country CLOB DEFAULT NULL, country_of_residence CLOB DEFAULT NULL, place_of_birth CLOB DEFAULT NULL, immigration_review_progress INTEGER NOT NULL, phone CLOB DEFAULT NULL, shirt_size INTEGER DEFAULT NULL, shirt_fit INTEGER DEFAULT NULL, diet CLOB DEFAULT NULL, allergies CLOB DEFAULT NULL, single_room BOOLEAN DEFAULT NULL, event_presence_review_progress INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D79F6B1156CBBCF5 ON participant (delegation_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B113E0872DC ON participant (arrival_travel_group_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B113F2C9D61 ON participant (departure_travel_group_id)');
        $this->addSql('CREATE TABLE travel_group (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , arrival_or_departure INTEGER NOT NULL, location VARCHAR(255) DEFAULT NULL, date_time DATETIME DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, trip_number VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, review_progress INTEGER NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C56CC37356CBBCF5 ON travel_group (delegation_id)');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , is_admin BOOLEAN NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, password CLOB DEFAULT NULL, authentication_hash CLOB DEFAULT NULL, is_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D64956CBBCF5 ON user (delegation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE delegation');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE travel_group');
        $this->addSql('DROP TABLE user');
    }
}
