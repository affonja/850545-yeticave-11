<?php
require_once('init.php');

$categories = get_categories($connection);

$lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$lot_id) {
    header("Location: 404.php");
    $error = 'Лот не найден';
    $page_content = include_template('404.php', [
        'error'      => $error,
        'categories' => $categories
    ]);
}

$lot = get_lot($connection, $lot_id);
$bets = get_bets_for_lot($connection, $lot_id);
$last_better = $bets[0]['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $bet = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_INT);
    $error_bet = validate_bet_form($bet, $lot['min_next_bet'], $lot['owner_id'],
        (int)$_SESSION['id'], $last_better);

    if (!$error_bet) {
        add_bet($connection, (int)$bet, $lot_id, $_SESSION['id']);
        $lot = get_lot($connection, $lot_id);
        $bets = get_bets_for_lot($connection, $lot_id);
    }
}

if (!$lot) {
    http_response_code(404);
    $error = 'Лот не найден';
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