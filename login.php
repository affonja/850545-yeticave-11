<?php
require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_data = $_POST;
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
            header("Location: /index.php");
            exit();
        }
    }
} else {
    $page_content = include_template('/login.php', [
        'categories' => $categories,
        'errors'     => $error
    ]);
}

$session_id = isset($_SESSION['id']);
$access_error = validation_access_right($session_id, true,
    'Вход на сайт уже выполнен');

if ($access_error) {
    http_response_code(403);
    $page_content = include_template('404.php', [
        'categories' => $categories,
        'error'      => $access_error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Вход на сайт',
    'page_content' => $page_content,
    'categories'   => $categories
]));