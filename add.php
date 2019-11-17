<?php
require_once('init.php');

if ($_POST){
    $lot = $_POST;

    if (isset($_FILES['lot_img'])){
        $file_name = uniqid().'.jpg';
        $lot['img'] = '/uploads/'.$file_name;
        move_uploaded_file($_FILES['lot_img']['tmp_name'], $lot['img']);
    }
    $var = implode('@', $lot);

       $sql = <<<SQL
insert into lots (
  name, category_id, description,
  bet_start, bet_step, end_time, img, 
  owner_id 
)
values (
       $var 
)
SQL;

//При успешном сохранении формы, переадресовывать пользователя на страницу просмотра лота.
//    header("Location: /lot.php?success:true");
} else {

}

//Убедиться, что заполнены все поля.
//Выполнить все проверки.
//Если есть ошибки заполнения формы, то сохранить их в отдельном массиве.
//Если ошибок нет, то сохранить новый лот в таблице лотов, и сохранить ссылку.
//Список проверок
//
//Проверка изображения
//
//Обязательно проверять MIME-тип загруженного файла;
//Допустимые форматы файлов: jpg, jpeg, png;
//Для проверки сравнивать MIME-тип файла со значением «image/png», «image/jpeg»;
//Чтобы определить MIME-тип файла, использовать функцию mime_content_type.

//Проверка начальной цены
//
//Содержимое поля «начальная цена» должно быть числом больше нуля.
//Проверка даты завершения
//
//Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»;
//Проверять, что указанная дата больше текущей даты, хотя бы на один день.
//Проверка шага ставки
//
//Содержимое поля «шаг ставки» должно быть целым числом больше ноля.


$categories = getCategories($connection['link']);
if (is_array($categories)) {
    $page_content = include_template('main.php',
        ['categories' => $categories]);
} else {
    $page_content = include_template('error.php', ['error' => $categories]);
}

$page_content = include_template('add-lot.php', [
    'categories' => $categories
]);
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