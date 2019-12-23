<?php
require_once('init.php');

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($user_id) {
    $lots = get_lots_where_better($connection, $user_id);
    $is_winner = get_lots_where_winner($connection, $user_id);
    $win_bets = [];
    $contacts = [];
    if (!empty($is_winner)) {
        $win_bets = get_win_bets_for_user($connection, $is_winner);
        foreach ($is_winner as $win_lot) {
            $contacts[$win_lot] = get_contacts($connection, $win_lot);
        }
    }

    $page_content = include_template('my-bets.php', [
        'categories' => $categories,
        'lots'       => $lots,
        'user_id'    => $user_id,
        'win_bets'   => $win_bets,
        'contacts'   => $contacts
    ]);
} else {
    http_response_code(404);
    $error['header'] = '404 Страница не найдена';
    $error['message'] = '';
    $page_content = include_template('404.php', [
        'error'      => $error,
        'categories' => $categories
    ]);
}

$session_id = isset($_SESSION['id']);
$access_error = validation_access_right($session_id, false,
    'Пройдите авторизацию');

if (!$access_error) {
    $session_id = $_SESSION['id'] !== $user_id;
    $access_error = validation_access_right($session_id, true,
        'Пройдите авторизацию');
}

if ($access_error) {
    http_response_code(403);
    $page_content = include_template('404.php', [
        'categories' => $categories,
        'error'      => $access_error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Мои ставки',
    'page_content' => $page_content,
    'categories'   => $categories
]));