<?php
require_once('init.php');

$categories = getCategories($connection['link']);
if (is_array($categories)) {
    $page_content = include_template('main.php',
        ['categories' => $categories]);
} else {
    $page_content = include_template('error.php', ['error' => $categories]);
}

$lot_id = filter_input(INPUT_GET, 'id');
$lot = getLot($connection['link'], $lot_id);
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

print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));