<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = getCategories($connection['link'], $error);
    if (is_array($categories)) {
        $cat_ids = array_column($categories, 'id');
        $page_content = include_template('main.php',
            ['categories' => $categories]);
    } else {
        $page_content = include_template('error.php', ['error' => $categories]);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            'lot-name' => function ($value) {
                return validateLength($value, 3, 200);
            },
            'category' => function ($value) use ($cat_ids) {
                return validateCategory($value, $cat_ids);
            },
            'message'  => function ($value) {
                return validateLength($value, 10, 3000);
            },
            'lot-rate' => function ($value) {
                return validatePrice($value);
            },
            'lot-step' => function ($value) {
                return validateBetStep($value);
            },
            'lot-date' => function ($value) {
                if (is_date_valid($value) and
                    is_interval_valid($value, 'P1D')
                ) {
                    return null;
                } else {
                    return 'Выберите корректную дату';
                }
            }
        ];

        $lot = filter_input_array(INPUT_POST, [
            'lot-name' => FILTER_DEFAULT,
            'category' => FILTER_DEFAULT,
            'message'  => FILTER_DEFAULT,
            'lot-rate' => FILTER_VALIDATE_FLOAT,
            'lot-step' => FILTER_VALIDATE_INT,
            'lot-date' => FILTER_DEFAULT
        ], true);
        $errors = getValidateForm($lot, $rules, $errors, $required);
        $errors['file'] = getValidateFile($lot);
        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('add-lot.php', [
                'categories' => $categories,
                'errors'     => $errors,
                'lot'        => $lot
            ]);
        } else {
            $add_lot = getAddLot($connection['link'], $lot);
            if ($add_lot) {
                $lot_id = mysqli_insert_id($connection['link']);
                header("Location: lot.php?id=".$lot_id);
            }
        }
    } else {
        $page_content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors,
            'lot'        => $lot
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