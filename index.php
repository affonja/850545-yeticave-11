<?php
require_once('helpers.php');
require_once('function.php');
require_once('data.php');

$page_content = include_template('main.php', [
    'categories' => $categories,
    'lots' => $lots
]);

$layout = include_template('layout.php', [
    'page_title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'page_content' => $page_content,
    'categories' => $categories
]);

print($layout);
