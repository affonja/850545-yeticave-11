<?php
require_once('init.php');

$categories = get_categories($connection);

$lot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$lot = get_lot($connection, $lot_id);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $bet = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_INT);
    if (!$bet or $bet < $lot['min_next_bet']) {
        $error_bet = 'Введите корректную сумму';
    } else {
        get_add_bet($connection, $bet, $lot_id, $_SESSION['id']);
        $lot = get_lot($connection, $lot_id);
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
        'error_bet' => $error_bet ?? null
    ]);
}

print(include_template('layout.php', [
    'page_title'   => $lot['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));


//$bets = get_bets($connection['link'], $lot['id']);
//
//foreach ($bets as &$bet) {
//    $now = time();
//    $bet_time = strtotime($bet['creation_time']);
//    $diff_time = $now - $bet_time;
//    if ($diff_time > 86400) {
//        $bet['time_back'] = date('j.m.y в H:i', $bet_time);
//    } elseif ($diff_time < 3600) {
//        $time = 1 * date('i', $bet_time);
//        $bet['time_back'] = $time.' '.get_noun_plural_form($time,
//                'минута', 'минуты', 'минут').' назад';
//    } else {
//        $time = date('G', $bet_time);
//        $bet['time_back'] = $time.' '.get_noun_plural_form($time, 'час',
//                'часа', 'часов').' назад';
//    }
//}
//
//
//$page_content = include_template('lot.php', [
//    'categories' => $categories,
//    'lot'        => $lot,
//    'error_bet'  => $error_bet,
//    'bets'       => $bets
//]);
//}