#Напишите запросы для добавления информации в БД:

#Существующий список категорий;
	INSERT INTO categories (name, code)
	VALUES ('Доски и лыжи', 'boards'),
		 ('Крепления', 'attachment'),
		 ('Ботинки', 'boots'),
		 ('Одежда', 'clothing'),
		 ('Инструменты', 'tools'),
		 ('Разное', 'other');

#Придумайте пару пользователей;
	INSERT INTO users
	SET email = 'j_doe@gmail.com',
		 NAME = 'John Doe',
		 PASSWORD = 'pass1',
		 contacts = '1234567';
	INSERT INTO users
	SET email = 'ju_doe@ya.ru',
		 NAME = 'Judy Doe',
		 PASSWORD = 'pass2',
		 contacts = '7654321';

#Существующий список объявлений;
	INSERT INTO lots (date_create, NAME, img_url, bet_start, enddate, bet_step, autor, category)
	VALUES 
		('2019-11-01 20:23:19', 'DC Ply Mens 2016/2017 Snowboard',
		'img/img/lot-1.jpg', '10999', '2019-12-01 00:00:00', '100', '1', '1'),
		('2019-10-01 20:23:19', '2014 Rossignol District Snowboard',
		'img/img/lot-2.jpg', '15999', '2019-12-25 00:00:00', '50', '1', '1'),
		('2019-10-20 20:23:19', 'Крепления Union Contact Pro 2015 года размер L/XL',
		'img/img/lot-3.jpg', '8000', '2019-11-01 00:00:00', '500', '1', '2'),
		('2019-05-20 20:23:19', 'Ботинки для сноуборда DC Mutiny Charocal',
		'img/img/lot-4.jpg', '10999', '2019-12-10 00:00:00', '1000', '2', '3'),
		('2019-08-01 20:23:19', 'Куртка для сноуборда DC Mutiny Charocal',
		'img/img/lot-5.jpg', '10999', '2020-01-25 00:00:00', '100', '2', '4'),	 	 
		('2019-09-01 20:23:19', 'Маска Oakley Canopy',
		'img/img/lot-6.jpg', '5500', '2020-01-20 00:00:00', '100', '2', '6');

#Добавьте пару ставок для любого объявления.
INSERT INTO bets (DATE, summ, USER, lot)
VALUES 
	('2019-11-02 20:23:19', '11500', '2', '1'),
	('2019-11-02 20:23:19', '16200', '2', '2'),
	('2019-11-03 12:21:19', '11700', '1', '1'),
	('2019-11-03 14:27:53', '12200', '2', '1'),
	('2019-11-04 12:22:26', '16800', '1', '2');

#Напишите запросы для этих действий:

#получить все категории;
	SELECT * FROM categories;	

#получить самые новые, открытые лоты. Каждый лот должен включать название, 
#стартовую цену, ссылку на изображение, цену, название категории;
	SELECT 
		l.name, l.bet_start,
		l.img_url, b.summ,
		cat.name
	FROM lots l
	INNER JOIN categories cat ON l.category = cat.id
	LEFT JOIN bets b ON l.id=b.lot AND 
		b.summ=(SELECT MAX(b.summ) FROM bets b WHERE b.lot=l.id)
	WHERE l.enddate > NOW()
	ORDER BY l.date_create DESC LIMIT 3

#показать лот по его id. Получите также название категории, к которой принадлежит лот;
	SELECT l.NAME, c.name from lots l
	INNER JOIN categories c ON l.category=c.id
	WHERE l.id=5;

#обновить название лота по его идентификатору;
	UPDATE lots
	SET NAME='new name'
	WHERE id=5;

#получить список ставок для лота по его идентификатору с сортировкой по дате.
	SELECT summ, date bet FROM bets 
	WHERE lot=1
	ORDER BY DATE DESC;