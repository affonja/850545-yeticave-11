<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('404.php',
        ['error' => $connection['error']]);
} else {

    $categories = getCategories($connection['link'], $error);
    if ($categories) {
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('404.php', ['error' => $error]);
    }

    $lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $lot = getLot($connection['link'], $lot_id);
    if (!$lot_id or !is_array($lot)) {
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
}
print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));