<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('404.php',
        ['error' => $connection['error']]);
} else {
    $categories = getCategories($connection['link'], $error);
    if (!is_array($categories)) {
        $categories = $error;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];
        $user_data = getFormData($_POST);
        $errors['email'] = validateEmail2($user_data['email'],
            $connection['link']);
        if ($errors['email'] === null) {
            $errors['password'] = validatePass2($user_data['password'],
                $connection['link'], $user_data['email']);
        }
        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('/login.php', [
                'categories' => $categories,
                'errors'     => $errors
            ]);
        } else {
            $user = getUser($connection['link'],$user_data['email']);
            $_SESSION['user'] = $user['name'];
            header("Location: index.php");
            exit();
        }


    } else {
        $page_content = include_template('/login.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
//        if (isset($_SESSION['user'])) {
//            header ("Location: /index.php");
//            exit();
//        }

    }

}

print(include_template('layout.php', [
    'page_title'   => 'Вход на сайт' ?? 'Ошибка',
    'user_name'    => '',
    'page_content' => $page_content,
    'categories'   => $categories
]));
