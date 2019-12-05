<?php
require_once('init.php');

$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_data = get_user_form_login_data($_POST);
    $errors = validate_login_form($connection, $user_data);

    if (count($errors)) {
        $page_content = include_template('/login.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    } else {
        $user = get_user($connection, $user_data['email']);
        if ($user) {
            $_SESSION = [
                'user' => $user['name'],
                'id'   => $user['id']
            ];
            header("Location: index.php");
            exit();
        }
    }
} else {
    $page_content = include_template('/login.php', [
        'categories' => $categories,
        'errors'     => $error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Вход на сайт',
    'page_content' => $page_content,
    'categories'   => $categories
]));