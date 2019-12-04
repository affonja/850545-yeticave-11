<?php
require_once('init.php');

$categories = get_categories($connection);

$lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$lot = get_lot($connection, $lot_id);

if (!$lot_id) {
    http_response_code(404);
    $error = 'Лот не найден';
    $page_content = include_template('404.php', [
        'error'      => $error,
        'categories' => $categories
    ]);
} else {
    $page_content = include_template('lot.php', [
        'categories' => $categories,
        'lot'        => $lot
    ]);
}

print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));