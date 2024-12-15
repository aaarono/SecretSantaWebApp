CREATE TABLE "User"(
    login varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    password_hash varchar(255) NOT NULL,
    first_name varchar(255) NOT NULL,
    last_name varchar(255) NOT NULL,
    phone varchar(20),
    gender varchar(20) NOT NULL,
    role varchar(20) NOT NULL DEFAULT 'regular'::character varying,
    created_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    language varchar(50),
    profile_photo bytea,
    PRIMARY KEY(login),
    CONSTRAINT User_role_check CHECK (((role)::text = ANY ((ARRAY['regular'::character varying, 'admin'::character varying])::text[])))
);

CREATE TABLE "Game" (
    UUID UUID PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Budget NUMERIC(10, 2),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    EndsAt TIMESTAMP NOT NULL,
    Status VARCHAR(20) CHECK (Status IN ('running', 'ended', 'pending')) DEFAULT 'pending',
    creator_login VARCHAR(255) NOT NULL,
    FOREIGN KEY (creator_login) REFERENCES "User"(login)
);

CREATE TABLE "Wishlist" (
    id SERIAL PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    URL VARCHAR(255),
    Login VARCHAR(255),
    FOREIGN KEY (Login) REFERENCES "User"(login)
);

CREATE TABLE "Player_Game" (
    login VARCHAR(255) NOT NULL,
    UUID UUID NOT NULL,
    is_gifted BOOLEAN DEFAULT FALSE NOT NULL,
    PRIMARY KEY (login, UUID),
    FOREIGN KEY (login) REFERENCES "User"(login),
    FOREIGN KEY (UUID) REFERENCES "Game"(UUID) ON DELETE CASCADE -- Каскадное удаление
);

CREATE TABLE "Pair" (
    id SERIAL PRIMARY KEY,
    game_id UUID,
    gifter_id VARCHAR(255),
    receiver_id VARCHAR(255),
    FOREIGN KEY (game_id) REFERENCES "Game"(UUID) ON DELETE CASCADE, -- Каскадное удаление
    FOREIGN KEY (gifter_id) REFERENCES "User"(login),
    FOREIGN KEY (receiver_id) REFERENCES "User"(login)
);

CREATE TABLE "SMS" (
    id SERIAL PRIMARY KEY,
    game_id UUID NOT NULL,
    login VARCHAR(255) NOT NULL,
    message_encrypted TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES "Game"(UUID) ON DELETE CASCADE,
    FOREIGN KEY (login) REFERENCES "User"(login) ON DELETE CASCADE
);