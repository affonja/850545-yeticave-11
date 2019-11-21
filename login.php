<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = getCategories($connection['link'], $error);
    if (!is_array($categories)){
        $categories= $error;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $errors = [];
        $required = [
            'lot-name',
            'category',
            'message',
            'lot-rate',
            'lot-step',
            'lot-date'
        ];
        $rules = [
            'email'    => function ($value) use ($connection) {
                return validateEmail2($value, $connection['link']);
            }
        ];

        $user_data = getFormData($_POST);
        $errors = getValidateForm($user_data, $rules,$errors,$required);





        if (count($errors)) {
            $page_content = include_template('/login.php', [
                'categories' => $categories,
                'errors'     => $errors
            ]);
        } else {
//                header("Location: lot.php?id=".$lot_id);
        }




    } else{
        $page_content = include_template('login.php', [
            'categories' => $categories,
            'errors'     => $errors
        ]);
    }

}

print(include_template('layout.php', [
    'page_title'   => 'Вход на сайт' ?? 'Ошибка',
    'user_name'    => '',
    'page_content' => $page_content,
    'categories'   => $categories
]));
