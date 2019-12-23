<?php
require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_data = $_POST;
    $errors = validate_reg_form($connection, $user_data);

    if (count($errors)) {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    } else {
        $is_user_added = add_user($connection, $user_data);
        if ($is_user_added) {
            header("Location: /login.php");
        }
    }
} else {
    $page_content = include_template('sign-up.php', [
        'categories' => $categories,
        'errors'     => $error
    ]);
}

$session_id = isset($_SESSION['id']);
$access_error = validation_access_right($session_id, true,
    'Пользователь уже авторизован');

if ($access_error) {
    http_response_code(403);
    $page_content = include_template('404.php', [
        'categories' => $categories,
        'error'      => $access_error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Регистрация',
    'page_content' => $page_content,
    'categories'   => $categories
]));