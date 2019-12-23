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

if (isset($_SESSION['id'])) {
    http_response_code(403);
    $error['header'] = '403 Доступ запрещен';
    $error['message'] = 'Вход на сайт уже выполнен';
    $page_content = include_template('404.php', [
        'categories' => $categories,
        'error'      => $error
    ]);
}

print(include_template('layout.php', [
    'page_title'   => 'Вход на сайт',
    'page_content' => $page_content,
    'categories'   => $categories
]));