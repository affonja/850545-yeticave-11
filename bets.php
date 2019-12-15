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
$is_winner = get_lots_where_winner($connection, $user_id);
if (!empty($is_winner)) {
    $win_bets = get_win_bets_for_user($connection, $is_winner);
    foreach ($is_winner as $win_lot){
        $contacts[$win_lot] = get_contacts($connection, $win_lot);
    }
}

$page_content = include_template('my-bets.php', [
    'error'      => $error,
    'categories' => $categories,
    'bets'       => $bets,
    'user_id'    => $user_id,
    'win_bets'   => $win_bets ?? [],
    'contacts' => $contacts ?? []
]);


print(include_template('layout.php', [
    'page_title'   => 'Мои ставки',
    'page_content' => $page_content,
    'categories'   => $categories
]));