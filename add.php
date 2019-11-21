<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = get_сategories($connection['link'], $error);
    $categories = get_сategories($connection['link'], $error);
    if (!is_array($categories)) {
        $categories = $error;
    } else{
        $cat_ids = array_column($categories, 'id');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file_data = $_FILES['lot_img']['name'];
        $lot_data = get_lot_form_data($_POST);
        $errors = validate_lot_form($lot_data, $file_data, $cat_ids);

        if (count($errors)) {
            $page_content = include_template('add-lot.php', [
                'categories' => $categories,
                'errors'     => $errors,
                'lot'        => $lot_data
            ]);
        } else {
            $lot_id = add_lot($connection['link'], $lot_data);
            if ($lot_id) {
                header("Location: lot.php?id=".$lot_id);
            }
        }
    } else {
        $page_content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors,
            'lot'        => $lot_data
        ]);
    }
}


print(include_template('layout.php', [
    'page_title'   => 'Добавить лот' ?? 'Ошибка',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));