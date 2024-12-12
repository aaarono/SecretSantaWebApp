CREATE TABLE "User" (
    login VARCHAR(255) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    gender VARCHAR(20),
    profile_photo VARCHAR(255),
    role VARCHAR(20) CHECK (role IN ('regular', 'admin')) DEFAULT 'regular',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    language VARCHAR(50)
);

CREATE TABLE "Game" (
    UUID UUID PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Budget NUMERIC(10, 2),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    EndsAt TIMESTAMP NOT NULL,
    Status VARCHAR(20) CHECK (Status IN ('running', 'ended')) DEFAULT 'running'
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
    login VARCHAR(255),
    UUID UUID,
    is_gifted BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (login, UUID),
    FOREIGN KEY (login) REFERENCES "User"(login),
    FOREIGN KEY (UUID) REFERENCES "Game"(UUID)
);

CREATE TABLE "Pair" (
    id SERIAL PRIMARY KEY,
    game_id UUID,
    gifter_id VARCHAR(255),
    receiver_id VARCHAR(255),
    FOREIGN KEY (game_id) REFERENCES "Game"(UUID),
    FOREIGN KEY (gifter_id) REFERENCES "User"(login),
    FOREIGN KEY (receiver_id) REFERENCES "User"(login)
);
