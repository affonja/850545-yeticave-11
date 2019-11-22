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

    $lots = getActiveLots($connection['link'], $error);
    if ($lots) {
        $page_content = include_template('main.php', [
            'categories' => $categories,
            'lots'       => $lots
        ]);
    } else {
        $page_content = include_template('404.php', ['error' => $error]);
    }
}

print(include_template('layout.php', [
    'page_title'   => 'Главная',
    'page_content' => $page_content,
    'categories'   => $categories
]));