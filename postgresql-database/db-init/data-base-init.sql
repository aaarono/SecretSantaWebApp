-- Creation of schemas and types
CREATE SCHEMA IF NOT EXISTS secret_santa;
SET search_path TO secret_santa;

CREATE TYPE gender_enum AS ENUM ('male', 'female', 'other');
CREATE TYPE role_enum AS ENUM ('regular', 'admin');
CREATE TYPE status_enum AS ENUM ('running', 'ended');

-- Creating extension for UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Table User
CREATE TABLE "User" (
    login VARCHAR(50) PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    gender gender_enum,
    profile_photo BYTEA,
    role role_enum DEFAULT 'regular',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    language VARCHAR(10),
    theme VARCHAR(20)
);

-- Function to update the updated_at field
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

-- Trigger for the User table
CREATE TRIGGER update_user_updated_at
BEFORE UPDATE ON "User"
FOR EACH ROW
EXECUTE PROCEDURE update_updated_at_column();


-- Table Wishlist
CREATE TABLE "Wishlist" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    url VARCHAR(255),
    login VARCHAR(50) NOT NULL,
    FOREIGN KEY (login) REFERENCES "User"(login) ON DELETE CASCADE
);

-- Table Game
CREATE TABLE "Game" (
    uuid UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL,
    description TEXT,
    budget NUMERIC(10,2),
    theme VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ends_at TIMESTAMP,
    status status_enum DEFAULT 'running'
);

-- Table Player_Game
CREATE TABLE "Player_Game" (
    login VARCHAR(50) NOT NULL,
    uuid UUID NOT NULL,
    is_gifted BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (login, uuid),
    FOREIGN KEY (login) REFERENCES "User"(login) ON DELETE CASCADE,
    FOREIGN KEY (uuid) REFERENCES "Game"(uuid) ON DELETE CASCADE
);

-- Table Pair
CREATE TABLE "Pair" (
    game_uuid UUID NOT NULL,
    gifter_login VARCHAR(50) NOT NULL,
    receiver_login VARCHAR(50) NOT NULL,
    PRIMARY KEY (game_uuid, gifter_login),
    FOREIGN KEY (game_uuid) REFERENCES "Game"(uuid) ON DELETE CASCADE,
    FOREIGN KEY (gifter_login) REFERENCES "User"(login) ON DELETE CASCADE,
    FOREIGN KEY (receiver_login) REFERENCES "User"(login) ON DELETE CASCADE
);
