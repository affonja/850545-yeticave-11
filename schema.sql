CREATE DATABASE yeticave;
USE yeticave;

CREATE TABLE categories
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHARACTER (50) NOT NULL UNIQUE,
    code VARCHARACTER (50) NOT NULL UNIQUE
);

CREATE TABLE lots
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    name VARCHARACTER (128) NOT NULL,
    description   TEXT,
    img VARCHARACTER (255),
    bet_start     INT       NOT NULL,
    end_time      TIMESTAMP NOT NULL,
    bet_step      INT       NOT NULL,
    KEY name_idx (name),
    KEY creation_time_idx (creation_time),
    KEY end_time_idx (end_time),
    owner_id      INT       NOT NULL,
    winner_id     INT,
    category_id   INT       NOT NULL
);

CREATE TABLE bets
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    sum           INT NOT NULL,
    user_id       INT NOT NULL,
    lot_id        INT NOT NULL
);

CREATE TABLE users
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    email VARCHARACTER (64) NOT NULL UNIQUE,
    name VARCHARACTER (64) NOT NULL,
    password VARCHARACTER (64) NOT NULL,
    contacts VARCHARACTER (255) NOT NULL
);

ALTER TABLE lots
    ADD CONSTRAINT fk_user FOREIGN KEY (owner_id) REFERENCES users (id),
    ADD CONSTRAINT fk_winner FOREIGN KEY (winner_id) REFERENCES users (id),
    ADD CONSTRAINT fk_cat FOREIGN KEY (category_id) REFERENCES categories (id);

ALTER TABLE bets
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (id),
    ADD CONSTRAINT fk_lot_id FOREIGN KEY (lot_id) REFERENCES lots (id);