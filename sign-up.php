<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = get_categories($connection['link'], $error);
    if (is_array($categories)) {
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('error.php', ['error' => $categories]);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $user_data = get_user_form_data($_POST);
        $errors = validate_reg_form($user_data, $connection['link']);

        if (count($errors)) {
            $page_content = include_template('/sign-up.php', [
                'categories' => $categories,
                'errors'     => $errors
            ]);
        } else {
            $is_user_added = add_user($connection['link'], $user_data);
            if ($is_user_added) {
                header("Location: /pages/login.html");
            }
        }

    } else {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'errors'     => $error
        ]);
    }
}


print(include_template('layout.php', [
    'page_title'   => 'Регистрация',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));