<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = getCategories($connection['link'], $error);
    if (is_array($categories)) {
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('error.php', ['error' => $categories]);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];
        $required = [
            'email',
            'password',
            'name',
            'message'
        ];
        $rules = [
            'email'    => function ($value) use ($connection) {
                return validateEmail($value, $connection['link']);
            },
            'password' => function ($value) {
                return validatePass($value);
            },
            'name'     => function ($value) {
                return validateLength($value, 3, 200);
            },
            'message'  => function ($value) {
                return validateLength($value, 10, 3000);
            }
        ];

        $user = filter_input_array(INPUT_POST, [
            'email'    => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_DEFAULT,
            'name'     => FILTER_DEFAULT,
            'message'  => FILTER_DEFAULT
        ], true);
        $errors = getValidateForm($user, $rules, $errors, $required);

        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('/sign-up.php', [
                'categories' => $categories,
                'errors'     => $errors
            ]);
        } else {
            $add_user = getAddUser($connection['link'], $user);
            if ($add_user) {
                header("Location: /pages/login.html");
            }
        }

    } else {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    }
}


print(include_template('layout.php', [
    'page_title'   => 'Регистрация' ?? 'Ошибка',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));