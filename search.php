<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('404.php',
        ['error' => $connection['error']]);
} else {
    $categories = get_categories($connection['link'], $error);
    if (!is_array($categories)) {
        $categories = $error;
    }

    $cur_page = $_GET['page'] ?? 1;
    $item_per_page = 6;
    $search_query = $_GET['search'] ?? '';
    $lots_count = get_lots_count($connection['link'], $search_query);

    if (is_int($lots_count)) {
        $pages_count = intval(ceil($lots_count / $item_per_page));
        $offset = ($cur_page - 1) * $item_per_page;
        $pages = range(1, $pages_count);
        $lots = get_searching_lots($connection['link'], $search_query,
            $item_per_page, $offset);

        $page_content = include_template('search.php', [
            'categories' => $categories,
            'pages'      => $pages,
            'cur_page'   => $cur_page,
            'lots'       => $lots,
            'query'      => $search_query,
        ]);
    } else {
        $page_content = include_template('search.php', [
            'categories' => $categories,
            'pages'      => 1,
            'lots'       => null,
            'query'      => $search_query,
            'error'      => $lots_count
        ]);
    }
}

print(include_template('layout.php', [
    'page_title'   => 'Поиск',
    'page_content' => $page_content,
    'categories'   => $categories
]));