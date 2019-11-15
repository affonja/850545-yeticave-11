<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {

    $categories = getCategories($connection['link']);
    if (is_array($categories)) {
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('error.php', ['error' => $categories]);
    }

    $lots = getActiveLots($connection['link']);
    if (is_array($lots)) {
        $page_content = include_template('main.php', [
            'categories' => $categories,
            'lots'       => $lots
        ]);
    } else {
        $page_content = include_template('error.php', ['error' => $lots]);
    }
}

print(include_template('layout.php', [
    'page_title'   => 'Главная',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));