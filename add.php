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
                return validateLength($value, 3, 50);
            },
            'category' => function ($value) use ($cat_ids) {
                return validateCategory($value, $cat_ids);
            },
            'message'  => function ($value) {
                return validateLength($value, 10, 1000);
            },
            'lot-rate' => function ($values){
                return validateNumber($values);
            },
            'lot-step' => function ($values){
                return validateNumber($values);
            },
            'lot-date' => function ($values){
                return is_date_valid($values);
            }
        ];
//        $lot = $_POST;
        $lot = filter_input_array(INPUT_POST, [
            'lot-name' => FILTER_DEFAULT,
            'category' => FILTER_DEFAULT,
            'message'  => FILTER_DEFAULT,
            'lot-rate' => FILTER_VALIDATE_INT,
            'lot-step' => FILTER_VALIDATE_INT,
            'lot-date' => FILTER_DEFAULT
        ], true);

        foreach ($lot as $field => $value) {
            if (isset($rules[$field])) {
                $rule = $rules[$field];
                $errors[$field] = $rule($value);
            }

            if (in_array($field, $required) && empty($value)) {
                $errors[$field] = "Заполните поле";
            }
        }
        $errors = array_filter($errors);

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
                $file_name = uniqid().'.jpg';
                $lot['img'] = 'uploads/'.$file_name;
                move_uploaded_file($_FILES['lot_img']['tmp_name'], $lot['img']);
            }
        } else {
            $errors['file'] = 'Не загружен файл';
        }

        if (count($errors)) {
            $page_content = include_template('add-lot.php', [
                'categories' => $categories,
                'errors'     => $errors,
                'lot'        => $lot
            ]);
        } else {


            $lot_id = mysqli_insert_id($connection['link']);
//            header("Location: lot.php?id=".$lot_id);
        }
    } else {
        $page_content = include_template('add-lot.php', [
            'categories' => $categories,
            'errors'     => $errors,
            'lot'        => $lot
        ]);
    }

}


//            $sql = <<<SQL
//INSERT INTO lots (
//name, category_id, description,
//bet_start, bet_step, end_time,
//img, creation_time,  owner_id )
//VALUES (
//       ?,?,?,?,?,?,?,NOW(),1
//)
//SQL;
//            $stmt = db_get_prepare_stmt($connection['link'], $sql, $lot);
//            $result = mysqli_stmt_execute($stmt);
//            if ($result) {
//                $lot_id = mysqli_insert_id($connection['link']);
////            header("Location: lot.php?id=".$lot_id);
//            }
//        }
//    }

//Убедиться, что заполнены все поля.
//Выполнить все проверки.
//Если есть ошибки заполнения формы, то сохранить их в отдельном массиве.
//Если ошибок нет, то сохранить новый лот в таблице лотов, и сохранить ссылку.

//Список проверок

//Проверка изображения
//Обязательно проверять MIME-тип загруженного файла;
//Допустимые форматы файлов: jpg, jpeg, png;
//Для проверки сравнивать MIME-тип файла со значением «image/png», «image/jpeg»;
//Чтобы определить MIME-тип файла, использовать функцию mime_content_type.

//Проверка начальной цены
////Содержимое поля «начальная цена» должно быть числом больше нуля.

//Проверка даты завершения
//Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»;
//Проверять, что указанная дата больше текущей даты, хотя бы на один день.

//Проверка шага ставки
//Содержимое поля «шаг ставки» должно быть целым числом больше ноля.


//    $page_content = include_template('add-lot.php', [
//        'categories' => $categories,
//        'arr_err'    => $errors
//    ]);
//http_response_code(404);
//$page_content = include_template('404.php', [
//    'error'      => $lot,
//    'categories' => $categories
//]);


print(include_template('layout.php', [
    'page_title'   => 'Добавить лот' ?? 'Ошибка',
    'is_auth'      => $is_auth,
    'user_name'    => $user_name,
    'page_content' => $page_content,
    'categories'   => $categories
]));