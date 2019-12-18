<?php
require_once('init.php');

$categories = get_categories($connection);

$cur_page = $_GET['page'] ?? 1;
$item_per_page = 9;
$category_id = filter_input(INPUT_GET, 'catid', FILTER_VALIDATE_INT) ?? 0;
$lots_count = get_lots_by_cat_count($connection, $category_id);

if (is_int($lots_count)) {
    $pages_count = intval(ceil($lots_count / $item_per_page));
    $offset = ($cur_page - 1) * $item_per_page;
    $pages = range(1, $pages_count);
    $lots = get_lots_by_category($connection, $category_id,
        $item_per_page, $offset);
    foreach ($lots as &$lot) {
        $lot['bet_count'] = get_count_bets_for_lot($connection, $lot['id']);
    }

    $page_content = include_template('all-lots.php', [
        'categories'    => $categories,
        'pages'         => $pages,
        'cur_page'      => $cur_page,
        'lots'          => $lots,
        'category_name' => $categories[$category_id - 1]['name'],
        'category_id'   => $category_id
    ]);
} else {
    $page_content = include_template('all-lots.php', [
        'categories' => $categories,
        'error'      => $lots_count,
        'category_name' => $categories[$category_id - 1]['name'],
        'lots'          => null,
        'pages'         => null
    ]);
}

print(include_template('layout.php', [
    'page_title'   => $categories[$category_id - 1]['name'] ?? 'Ошибка',
    'page_content' => $page_content,
    'categories'   => $categories
]));