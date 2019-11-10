# Существующий список категорий;
	INSERT INTO categories (name, code)
	VALUES  ('Доски и лыжи', 'boards'),
		('Крепления', 'attachment'),
		('Ботинки', 'boots'),
		('Одежда', 'clothing'),
		('Инструменты', 'tools'),
		('Разное', 'other');

# Придумайте пару пользователей;
	INSERT INTO users
	SET
		email = 'j_doe@gmail.com',
		name = 'John Doe',
		password = 'pass1',
		contacts = '1234567';
	INSERT INTO users
	SET 
		email = 'ju_doe@ya.ru',
		name = 'Judy Doe',
		password = 'pass2',
		contacts = '7654321';

# Существующий список объявлений;
	INSERT INTO lots (creation_time, name, img, bet_start, end_time, bet_step, owner_id, category_id)
	VALUES
		('2019-11-01 20:23:19', 'DC Ply Mens 2016/2017 Snowboard',
		'/img/lot-1.jpg', 10999, '2019-12-01 00:00:00', 100, 1, 1),
		('2019-10-01 20:23:19', '2014 Rossignol District Snowboard',
		'/img/lot-2.jpg', 15999, '2019-12-25 00:00:00', 50, 1, 1),
		('2019-10-20 20:23:19', 'Крепления Union Contact Pro 2015 года размер L/XL',
		'/img/lot-3.jpg', 8000, '2019-11-01 00:00:00', 500, 1, 2),
		('2019-05-20 20:23:19', 'Ботинки для сноуборда DC Mutiny Charocal',
		'/img/lot-4.jpg', 10999, '2019-12-10 00:00:00', 1000, 2, 3),
		('2019-08-01 20:23:19', 'Куртка для сноуборда DC Mutiny Charocal',
		'/img/lot-5.jpg', 10999, '2020-01-25 00:00:00', 100, 2, 4),
		('2019-09-01 20:23:19', 'Маска Oakley Canopy',
		'/img/lot-6.jpg', 5500, '2020-01-20 00:00:00', 100, 2, 6);

# Добавьте пару ставок для любого объявления.
	INSERT INTO bets (creation_time, sum, user_id, lot_id)
	VALUES
		('2019-11-02 20:23:19', 11500, 2, 1),
		('2019-11-02 20:23:19', 16200, 2, 2),
		('2019-11-03 12:21:19', 11700, 1, 1),
		('2019-11-03 14:27:53', 12200, 2, 1),
		('2019-11-04 12:22:26', 16800, 1, 2);

# Напишите запросы для этих действий:

# получить все категории;
	SELECT id, name FROM categories;

# получить самые новые, открытые лоты. Каждый лот должен включать название,
# стартовую цену, ссылку на изображение, цену, название категории;
	SELECT
		l.id, l.name, l.bet_start,
		l.img, MAX(b.sum),
		c.name
	FROM lots l
	INNER JOIN categories c ON l.category_id = c.id
	LEFT JOIN bets b ON l.id = b.lot_id
	WHERE l.end_time > NOW()
    GROUP BY l.id
	ORDER BY l.creation_time DESC LIMIT 3

# показать лот по его id. Получите также название категории, к которой принадлежит лот;
	SELECT
		l.creation_time, l.name, l.img,
		l.bet_start, l.end_time, l.bet_step,
		l.owner_id, l.category_id, c.name AS category_name
	FROM lots l
	INNER JOIN categories c ON l.category_id = c.id
	WHERE l.id = 5;

# обновить название лота по его идентификатору;
	UPDATE lots
	SET name = 'new name'
	WHERE id = 5;

# получить список ставок для лота по его идентификатору с сортировкой по дате.
	SELECT sum, creation_time FROM bets
	WHERE lot_id = 1
	ORDER BY creation_time  DESC;