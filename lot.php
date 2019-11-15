<?php
require_once('init.php');

$categories = getCategories($connection['link']);
if (is_array($categories)) {
    $page_content = include_template('main.php',
        ['categories' => $categories]);
} else {
    $page_content = include_template('error.php', ['error' => $categories]);
}


$lot_id = 2;

$lot = getLot($connection['link'],$lot_id);

print(include_template('lot.php', [
    'categories'   => $categories,
    'lot' => $lot
]));