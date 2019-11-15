<?php

function price_format(int $price): string
{
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, '.', ' ');
    }
    return $price;
}

function get_time_remaining(string $time): array
{
    $time_now = time();
    $time_end = strtotime($time);
    $time_diff = $time_end - $time_now;
    if ($time_diff < 0) {
        $time_diff = 0;
    }

    $time_remaining = [
        floor($time_diff / 3600),
        floor(($time_diff % 3600) / 60),
    ];

    return $time_remaining;
}

function dbConnect(array $db): array
{
    $connection = [
        'link'  => '',
        'error' => ''
    ];

    $connection['link'] = mysqli_connect($db['host'], $db['user'],
        $db['password'], $db['database']);
    if (!$connection['link']) {
        $connection['error'] = mysqli_connect_error();
    } else {
        mysqli_set_charset($connection['link'], "utf8");
    }
    return $connection;
}

function getCategories($connection)
{
    $sql = 'SELECT id, name, code FROM categories';
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $result = mysqli_error($connection);
    } else {
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return $result;
}

function getActiveLots($connection)
{

    $sql = <<<SQL
	SELECT
		l.id, l.name, l.bet_start,
		l.img, l.end_time,
		c.name AS category
	FROM lots l
	INNER JOIN categories c ON l.category_id = c.id
	LEFT JOIN bets b ON l.id = b.lot_id
	WHERE l.end_time > NOW()
    GROUP BY l.id
	ORDER BY l.creation_time DESC LIMIT 60
SQL;
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $result = mysqli_error($connection);
    } else {
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $result;
}

function getLot($connection, $id)
{
    $sql = <<<SQL
SELECT
		l.id, l.name, l.img, l.description,
        l.bet_start, l.bet_step,
		l.creation_time, l.end_time,
		c.name AS category,
		MAX(b.sum) as maxbet
FROM lots l
INNER JOIN categories c ON category_id = c.id
LEFT JOIN bets b ON b.lot_id = l.id
WHERE l.id = ?
    GROUP BY l.id
SQL;

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!mysqli_num_rows($result)) {
        $result = 'Лот не найден';
    } else {
        $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    return $result;
}
