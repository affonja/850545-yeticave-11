<?php
require_once('init.php');

$categories = get_categories($connection);

$lots = get_active_lots($connection);

$page_content = include_template('main.php', [
    'categories' => $categories,
    'lots'       => $lots
]);

print(include_template('layout.php', [
    'page_title'   => 'Yeticave',
    'page_content' => $page_content,
    'categories'   => $categories
]));