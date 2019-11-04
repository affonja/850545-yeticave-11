CREATE DATABASE Yeticave;
USE yeticave;

CREATE TABLE categories(
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHARACTER(50) NOT NULL UNIQUE,
	code VARCHARACTER(50) NOT NULL UNIQUE
);

CREATE TABLE lots(
	id INT AUTO_INCREMENT PRIMARY KEY,
	date_create TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
	NAME VARCHARACTER(128) NOT NULL,
	descript TEXT,
	img_url VARCHARACTER(255),
	bet_start FLOAT NOT NULL,
	enddate TIMESTAMP NOT NULL,
	bet_step FLOAT NOT NULL
);

CREATE TABLE bets(
	id INT AUTO_INCREMENT PRIMARY KEY,
	DATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
	summ FLOAT NOT NULL
);

CREATE TABLE users(
	id INT AUTO_INCREMENT PRIMARY KEY,	
	date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
	email VARCHARACTER(64) NOT NULL UNIQUE,
	NAME VARCHARACTER(64) NOT NULL,
	PASSWORD VARCHARACTER(64) NOT NULL,
	contacts VARCHARACTER(255) NOT NULL
);

ALTER TABLE lots
	ADD COLUMN autor INT,
	ADD COLUMN winner INT,
	ADD COLUMN category INT;

ALTER TABLE lots
	ADD CONSTRAINT fk_user FOREIGN KEY (autor) REFERENCES users(id),
	ADD CONSTRAINT fk_winner FOREIGN KEY (winner) REFERENCES users(id),
	ADD CONSTRAINT fk_cat FOREIGN KEY (category) REFERENCES categories(id);

ALTER TABLE bets
	ADD COLUMN user INT,
	ADD COLUMN lot INT,
	ADD CONSTRAINT fk_user_id FOREIGN KEY (user) REFERENCES users(id),
	ADD CONSTRAINT fk_lot_id FOREIGN KEY (lot) REFERENCES lots(id);

ALTER TABLE users
	ADD COLUMN lot INT,
	ADD COLUMN bet INT,
	ADD CONSTRAINT fk_lots FOREIGN KEY (lot) REFERENCES lots(id),
	ADD CONSTRAINT fk_bets FOREIGN KEY (bet) REFERENCES bets(id);