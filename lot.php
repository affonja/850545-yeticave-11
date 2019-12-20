<?php
require_once('init.php');

$lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 0;

$lot = get_lot($connection, $lot_id);
$bets = get_bets_for_lot($connection, $lot_id);
$last_better = $bets[0]['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $bet = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_INT);
    $error_bet = validate_bet_form($bet, $lot['min_next_bet'], $lot['owner_id'],
        $last_better, (int)$_SESSION['id']);

    if (!$error_bet) {
        add_bet($connection, (int)$bet, $lot_id, $_SESSION['id']);
        $lot = get_lot($connection, $lot_id);
        $bets = get_bets_for_lot($connection, $lot_id);
        $last_better = $bets[0]['user_id'];
    }
}

if (!$lot or !$lot_id) {
    http_response_code(404);
    $error['header'] = '404 Страница не найдена';
    $error['message'] = '';
    $page_content = include_template('404.php', [
        'error'      => $error,
        'categories' => $categories
    ]);
} else {
    $page_content = include_template('lot.php', [
        'categories' => $categories,
        'lot'        => $lot,
        'error_bet'  => $error_bet ?? null,
        'bets'       => $bets,
        'last_better' => $last_better
    ]);
}

print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));