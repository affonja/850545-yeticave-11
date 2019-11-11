<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
} else {
    $sql_cat = 'SELECT id, name, code FROM categories';
    $result_category = mysqli_query($link, $sql_cat);

    if ($result_category) {
        $categories = mysqli_fetch_all($result_category, MYSQLI_ASSOC);
        $page_content = include_template('main.php', ['categories' => $categories ]);
    } else {
        $error = mysqli_error($link);
        $page_content = include_template('error.php',['error' => $error]);
    }

    $sql_lot = <<<SQL
	SELECT
		l.id, l.name, l.bet_start,
		l.img, l.end_time,
		c.name AS category
	FROM lots l
	INNER JOIN categories c ON l.category_id = c.id
	LEFT JOIN bets b ON l.id = b.lot_id
	WHERE l.end_time > NOW()
    GROUP BY l.id
	ORDER BY l.creation_time DESC LIMIT 6
SQL;


    $result_lot = mysqli_query($link, $sql_lot);

    if ($result_lot) {
        $lots = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);
        $page_content = include_template('main.php', [
            'categories' => $categories,
            'lots' => $lots
        ]);
    } else {
        $error = mysqli_error($link);
        $page_content = include_template('error.php',['error' => $error]);
    }


}

print(include_template('index.php', [
    'page_content' => $page_content,
    'categories' => $categories,
    'page_title' => $page_title,
    'user_name' => $user_name,
    'is_auth' => $is_auth
]));
