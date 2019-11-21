<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {

    $categories = get_сategories($connection['link'], $error);
    if ($categories) {
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('error.php', ['error' => $error]);
    }

    $lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $lot = get_lot($connection['link'], $lot_id);
    if (!$lot_id or !is_array($lot)) {
        http_response_code(404);
        $page_content = include_template('404.php', [
            'error'      => $lot,
            'categories' => $categories
        ]);
    } else {
        $page_content = include_template('lot.php', [
            'categories' => $categories,
            'lot'        => $lot
        ]);
    }
}
print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));