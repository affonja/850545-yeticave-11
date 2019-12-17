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

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $bet = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_INT);
    if (!$bet or $bet < $lot['min_next_bet']) {
        $error_bet = 'Введите корректную сумму';
    } else {
        add_bet($connection, $bet, $lot_id, $_SESSION['id']);
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
        'bets'       => $bets
    ]);
}

print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));