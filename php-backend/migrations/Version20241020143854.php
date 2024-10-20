<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020143854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pair (id SERIAL NOT NULL, game_id INT NOT NULL, gifter_id INT NOT NULL, receiver_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_95A1E69E48FD905 ON pair (game_id)');
        $this->addSql('CREATE INDEX IDX_95A1E69609EF953 ON pair (gifter_id)');
        $this->addSql('CREATE INDEX IDX_95A1E69CD53EDB6 ON pair (receiver_id)');
        $this->addSql('CREATE TABLE player_game (id SERIAL NOT NULL, player_id INT NOT NULL, game_id INT NOT NULL, is_gifted BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_813161BF99E6F5DF ON player_game (player_id)');
        $this->addSql('CREATE INDEX IDX_813161BFE48FD905 ON player_game (game_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(30) NOT NULL, user_name VARCHAR(255) NOT NULL, last_name VARCHAR(30) NOT NULL, phone VARCHAR(20) NOT NULL, gender VARCHAR(10) NOT NULL, profile_photo BYTEA NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wishlist (id SERIAL NOT NULL, user_login_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CE12A31BC3F045D ON wishlist (user_login_id)');
        $this->addSql('ALTER TABLE pair ADD CONSTRAINT FK_95A1E69E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pair ADD CONSTRAINT FK_95A1E69609EF953 FOREIGN KEY (gifter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pair ADD CONSTRAINT FK_95A1E69CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player_game ADD CONSTRAINT FK_813161BF99E6F5DF FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player_game ADD CONSTRAINT FK_813161BFE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31BC3F045D FOREIGN KEY (user_login_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pair DROP CONSTRAINT FK_95A1E69E48FD905');
        $this->addSql('ALTER TABLE pair DROP CONSTRAINT FK_95A1E69609EF953');
        $this->addSql('ALTER TABLE pair DROP CONSTRAINT FK_95A1E69CD53EDB6');
        $this->addSql('ALTER TABLE player_game DROP CONSTRAINT FK_813161BF99E6F5DF');
        $this->addSql('ALTER TABLE player_game DROP CONSTRAINT FK_813161BFE48FD905');
        $this->addSql('ALTER TABLE wishlist DROP CONSTRAINT FK_9CE12A31BC3F045D');
        $this->addSql('DROP TABLE pair');
        $this->addSql('DROP TABLE player_game');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wishlist');
    }
}
