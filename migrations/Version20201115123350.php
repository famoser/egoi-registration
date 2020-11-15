<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115123350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE travel_group (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , arrival_or_departure INTEGER NOT NULL, location VARCHAR(255) NOT NULL, date_time DATETIME NOT NULL, identifier VARCHAR(255) NOT NULL, details VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C56CC37356CBBCF5 ON travel_group (delegation_id)');
        $this->addSql('CREATE TABLE travel_group_participant (travel_group_id CHAR(36) NOT NULL --(DC2Type:guid)
        , participant_id CHAR(36) NOT NULL --(DC2Type:guid)
        , PRIMARY KEY(travel_group_id, participant_id))');
        $this->addSql('CREATE INDEX IDX_DB3662639425BA02 ON travel_group_participant (travel_group_id)');
        $this->addSql('CREATE INDEX IDX_DB3662639D1C3019 ON travel_group_participant (participant_id)');
        $this->addSql('DROP INDEX UNIQ_292F436D5E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__delegation AS SELECT id, name, registration_hash, created_at, last_changed_at, leader_count, guest_count, participation_mode, attendance_review_progress, contribution_review_progress, translations, contestant_count FROM delegation');
        $this->addSql('DROP TABLE delegation');
        $this->addSql('CREATE TABLE delegation (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , name CLOB NOT NULL COLLATE BINARY, registration_hash VARCHAR(255) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, leader_count INTEGER NOT NULL, guest_count INTEGER NOT NULL, participation_mode INTEGER NOT NULL, attendance_review_progress INTEGER NOT NULL, contribution_review_progress INTEGER NOT NULL, translations CLOB DEFAULT NULL COLLATE BINARY --(DC2Type:simple_array)
        , contestant_count INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO delegation (id, name, registration_hash, created_at, last_changed_at, leader_count, guest_count, participation_mode, attendance_review_progress, contribution_review_progress, translations, contestant_count) SELECT id, name, registration_hash, created_at, last_changed_at, leader_count, guest_count, participation_mode, attendance_review_progress, contribution_review_progress, translations, contestant_count FROM __temp__delegation');
        $this->addSql('DROP TABLE __temp__delegation');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_292F436D5E237E06 ON delegation (name)');
        $this->addSql('DROP INDEX IDX_E7927C74A45BB98C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__email AS SELECT id, sent_by_id, identifier, type, sent_date_time, read_at FROM email');
        $this->addSql('DROP TABLE email');
        $this->addSql('CREATE TABLE email (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , sent_by_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:guid)
        , identifier CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , type INTEGER NOT NULL, sent_date_time DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_E7927C74A45BB98C FOREIGN KEY (sent_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO email (id, sent_by_id, identifier, type, sent_date_time, read_at) SELECT id, sent_by_id, identifier, type, sent_date_time, read_at FROM __temp__email');
        $this->addSql('DROP TABLE __temp__email');
        $this->addSql('CREATE INDEX IDX_E7927C74A45BB98C ON email (sent_by_id)');
        $this->addSql('DROP INDEX IDX_D79F6B1156CBBCF5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__participant AS SELECT id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress, gender, portrait, name_on_documents, papers, consent FROM participant');
        $this->addSql('DROP TABLE participant');
        $this->addSql('CREATE TABLE participant (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:guid)
        , role INTEGER NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, given_name CLOB DEFAULT NULL COLLATE BINARY, family_name CLOB DEFAULT NULL COLLATE BINARY, birthday DATETIME DEFAULT NULL, email CLOB DEFAULT NULL COLLATE BINARY, phone CLOB DEFAULT NULL COLLATE BINARY, personal_data_review_progress INTEGER NOT NULL, nationality CLOB DEFAULT NULL COLLATE BINARY, passport_number CLOB DEFAULT NULL COLLATE BINARY, passport_validity_from DATE DEFAULT NULL, passport_validity_to DATE DEFAULT NULL, passport_issue_country CLOB DEFAULT NULL COLLATE BINARY, country_of_residence CLOB DEFAULT NULL COLLATE BINARY, place_of_birth CLOB DEFAULT NULL COLLATE BINARY, immigration_review_progress INTEGER NOT NULL, diet CLOB DEFAULT NULL COLLATE BINARY, allergies CLOB DEFAULT NULL COLLATE BINARY, event_presence_review_progress INTEGER NOT NULL, gender INTEGER DEFAULT NULL, portrait CLOB DEFAULT NULL COLLATE BINARY, name_on_documents CLOB DEFAULT NULL COLLATE BINARY, papers CLOB DEFAULT NULL COLLATE BINARY, consent CLOB DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_D79F6B1156CBBCF5 FOREIGN KEY (delegation_id) REFERENCES delegation (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO participant (id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress, gender, portrait, name_on_documents, papers, consent) SELECT id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress, gender, portrait, name_on_documents, papers, consent FROM __temp__participant');
        $this->addSql('DROP TABLE __temp__participant');
        $this->addSql('CREATE INDEX IDX_D79F6B1156CBBCF5 ON participant (delegation_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX IDX_8D93D64956CBBCF5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:guid)
        , is_admin BOOLEAN NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL COLLATE BINARY, password CLOB DEFAULT NULL COLLATE BINARY, authentication_hash CLOB DEFAULT NULL COLLATE BINARY, is_enabled BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_8D93D64956CBBCF5 FOREIGN KEY (delegation_id) REFERENCES delegation (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled) SELECT id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D64956CBBCF5 ON user (delegation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE travel_group');
        $this->addSql('DROP TABLE travel_group_participant');
        $this->addSql('ALTER TABLE delegation ADD COLUMN location CLOB DEFAULT NULL COLLATE BINARY');
        $this->addSql('ALTER TABLE delegation ADD COLUMN travel_details CLOB DEFAULT NULL COLLATE BINARY');
        $this->addSql('ALTER TABLE delegation ADD COLUMN travel_review_progress INTEGER NOT NULL');
        $this->addSql('DROP INDEX IDX_E7927C74A45BB98C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__email AS SELECT id, sent_by_id, identifier, type, sent_date_time, read_at FROM email');
        $this->addSql('DROP TABLE email');
        $this->addSql('CREATE TABLE email (id CHAR(36) NOT NULL --(DC2Type:guid)
        , sent_by_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , identifier CHAR(36) NOT NULL --(DC2Type:guid)
        , type INTEGER NOT NULL, sent_date_time DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO email (id, sent_by_id, identifier, type, sent_date_time, read_at) SELECT id, sent_by_id, identifier, type, sent_date_time, read_at FROM __temp__email');
        $this->addSql('DROP TABLE __temp__email');
        $this->addSql('CREATE INDEX IDX_E7927C74A45BB98C ON email (sent_by_id)');
        $this->addSql('DROP INDEX IDX_D79F6B1156CBBCF5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__participant AS SELECT id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, gender, name_on_documents, portrait, papers, consent, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress FROM participant');
        $this->addSql('DROP TABLE participant');
        $this->addSql('CREATE TABLE participant (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , role INTEGER NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, given_name CLOB DEFAULT NULL, family_name CLOB DEFAULT NULL, birthday DATETIME DEFAULT NULL, email CLOB DEFAULT NULL, phone CLOB DEFAULT NULL, gender INTEGER DEFAULT NULL, name_on_documents CLOB DEFAULT NULL, portrait CLOB DEFAULT NULL, papers CLOB DEFAULT NULL, consent CLOB DEFAULT NULL, personal_data_review_progress INTEGER NOT NULL, nationality CLOB DEFAULT NULL, passport_number CLOB DEFAULT NULL, passport_validity_from DATE DEFAULT NULL, passport_validity_to DATE DEFAULT NULL, passport_issue_country CLOB DEFAULT NULL, country_of_residence CLOB DEFAULT NULL, place_of_birth CLOB DEFAULT NULL, immigration_review_progress INTEGER NOT NULL, diet CLOB DEFAULT NULL, allergies CLOB DEFAULT NULL, event_presence_review_progress INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO participant (id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, gender, name_on_documents, portrait, papers, consent, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress) SELECT id, delegation_id, role, created_at, last_changed_at, given_name, family_name, birthday, email, phone, gender, name_on_documents, portrait, papers, consent, personal_data_review_progress, nationality, passport_number, passport_validity_from, passport_validity_to, passport_issue_country, country_of_residence, place_of_birth, immigration_review_progress, diet, allergies, event_presence_review_progress FROM __temp__participant');
        $this->addSql('DROP TABLE __temp__participant');
        $this->addSql('CREATE INDEX IDX_D79F6B1156CBBCF5 ON participant (delegation_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX IDX_8D93D64956CBBCF5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL --(DC2Type:guid)
        , delegation_id CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , is_admin BOOLEAN NOT NULL, created_at DATETIME NOT NULL, last_changed_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, password CLOB DEFAULT NULL, authentication_hash CLOB DEFAULT NULL, is_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled) SELECT id, delegation_id, is_admin, created_at, last_changed_at, email, password, authentication_hash, is_enabled FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D64956CBBCF5 ON user (delegation_id)');
    }
}
