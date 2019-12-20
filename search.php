<?php
require_once('init.php');

$cur_page = $_GET['page'] ?? 1;
$item_per_page = 9;
$search_query = trim($_GET['search']) ?? '';
$lots_count = get_search_lots_count($connection, $search_query);

if (is_int($lots_count)) {
    $pages_count = intval(ceil($lots_count / $item_per_page));
    $offset = ($cur_page - 1) * $item_per_page;
    $pages = range(1, $pages_count);
    $lots = get_searching_lots($connection, $search_query,
        $item_per_page, $offset);
    foreach ($lots as &$lot) {
        $lot['bet_count'] = get_count_bets_for_lot($connection, $lot['id']);
    }

    $page_content = include_template('search.php', [
        'categories' => $categories,
        'pages'      => $pages,
        'cur_page'   => $cur_page,
        'lots'       => $lots,
        'query'      => $search_query
    ]);
} else {
    $page_content = include_template('search.php', [
        'categories' => $categories,
        'pages'      => [],
        'lots'       => null,
        'query'      => $search_query,
        'error'      => $lots_count
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Поиск',
    'page_content' => $page_content,
    'categories'   => $categories
]));