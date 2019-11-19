<?php
require_once('init.php');

if (!$connection['link']) {
    $page_content = include_template('error.php',
        ['error' => $connection['error']]);
} else {
    $categories = getCategories($connection['link']);
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
            'lot-rate' => function ($values) {
                return validatePrice($values);
            },
            'lot-step' => function ($values) {
                return validateBetStep($values);
            },
            'lot-date' => function ($values) {
                if ($dt = is_date_valid($values) and
                    $tmpp = getValidPeriod($values, '1D')
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

        getValidateForm();
        foreach ($lot as $field => $value) {
            if (isset($rules[$field])) {
                $rule = $rules[$field];
                $errors[$field] = $rule($value);
            }

            if (in_array($field, $required) && empty($value)) {
                $errors[$field] = "Заполните поле";
            }
        }

        if ($_FILES['lot_img']['name']) {
            $path = $_FILES['lot_img']['tmp_name'];
            $file_type = mime_content_type($path);
            $allow_type = [
                'image/png',
                'image/jpeg'
            ];

            if (!in_array($file_type, $allow_type)) {
                $errors['file'] = 'Неверный формат файла';
            } else {
                $file_name = $_FILES['lot_img']['name'];
                $ext = substr($file_name, strrpos($file_name, '.'));
                $file_name = uniqid().$ext;

                $lot['img'] = '/uploads/'.$file_name;
                move_uploaded_file($_FILES['lot_img']['tmp_name'], substr($lot['img'],1) );
            }
        } else {
            $errors['file'] = 'Не загружен файл';
        }
        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('add-lot.php', [
                'categories' => $categories,
                'errors'     => $errors,
                'lot'        => $lot
            ]);
        } else {
            $sql = <<<SQL
INSERT INTO lots (
name, category_id, description,
bet_start, bet_step, end_time,
img, creation_time,  owner_id )
VALUES (
       ?,?,?,?,?,?,?,NOW(),1
)
SQL;
            $stmt = db_get_prepare_stmt($connection['link'], $sql, $lot);
            $result = mysqli_stmt_execute($stmt);
            if ($result) {
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