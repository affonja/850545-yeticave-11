<?php
require_once('init.php');

$categories = get_categories($connection);
$cat_ids = array_column($categories, 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_data = $_FILES['lot_img'];
    $lot_data = get_lot_form_data($_POST, $_SESSION['id']);
    $errors = validate_lot_form($lot_data, $file_data, $cat_ids);

    if (count($errors)) {
        $page_content = include_template('/add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    } else {
        $lot_data['file'] = save_file($_FILES['lot_img']);
        $lot_id = add_lot($connection, $lot_data);
        if ($lot_id) {
            header("Location: lot.php?id=".$lot_id);
        }
    }
} else {
    $page_content = include_template('add-lot.php', [
        'categories' => $categories,
        'errors'     => $error
    ]);
}

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    $error = "Error 403 <br> Доступ запрещен";
    $page_content = include_template('404.php', [
        'categories' => $categories,
        'error'      => $error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Добавить лот',
    'page_content' => $page_content,
    'categories'   => $categories
]));