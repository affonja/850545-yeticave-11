<?php
require_once('init.php');

$categories = get_categories($connection);

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header("Location: 404.php");
    $error = 'Пользователь не найден';
    $page_content = include_template('404.php', [
        'error'      => $error,
        'categories' => $categories
    ]);
}

$bets = get_bets_for_user($connection, $user_id);

$page_content = include_template('my-bets.php', [
    'error'      => $error,
    'categories' => $categories,
    'bets'       => $bets,
    'user_id'    => $user_id
]);


print(include_template('layout.php', [
    'page_title'   => 'Мои ставки',
    'page_content' => $page_content,
    'categories'   => $categories
]));