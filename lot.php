<?php
require_once('init.php');

$categories = getCategories($connection['link']);
if (is_array($categories)) {
    $page_content = include_template('main.php',
        ['categories' => $categories]);
} else {
    $page_content = include_template('error.php', ['error' => $categories]);
}


$lot_id = filter_input(INPUT_GET, 'id');
if ($lot_id){
    $lot = getLot($connection['link'], $lot_id);
    $content = include_template('lot.php', [
        'categories' => $categories,
        'lot'        => $lot
    ]);
} else {
    $content = include_template('404.php', ['error' => 'Лот не найден']);
}

print($content);