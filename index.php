<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = get_сategories($connection['link'], $error);
    if (!is_array($categories)) {
        $categories = $error;
    }

    $lots = get_active_lots($connection['link'], $error);
    if ($lots) {
        $page_content = include_template('main.php', [
            'categories' => $categories,
            'lots'       => $lots
        ]);
    } else {
        $page_content = include_template('error.php', ['error' => $error]);
    }
}

print(include_template('layout.php', [
    'page_title'   => 'Главная',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));