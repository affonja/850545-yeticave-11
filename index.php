<?php
require_once('init.php');
require_once('getwinner.php');

$limit = 9;

$lots = get_active_lots($connection, $limit);
foreach ($lots as &$lot) {
    $lot['bet_count'] = get_count_bets_for_lot($connection, $lot['id']);
}

$page_content = include_template('main.php', [
    'categories' => $categories,
    'lots'       => $lots
]);

print(include_template('layout.php', [
    'page_title'   => 'Yeticave',
    'page_content' => $page_content,
    'categories'   => $categories
]));