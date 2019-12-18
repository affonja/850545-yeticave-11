<?php

/**
 * Фильтрует данные полученные из формы. Добавляет в массив id пользователя, отправившего форму
 *
 * @param $lot_data Массив с данными
 * @param  int  $id  id пользователя
 *
 * @return array Отфильтрованный массив данных
 */
function get_lot_form_data($lot_data, int $id): array
{
    $lot_data = filter_var_array($lot_data, [
        'lot-name' => FILTER_DEFAULT,
        'category' => FILTER_DEFAULT,
        'message'  => FILTER_DEFAULT,
        'lot-rate' => FILTER_VALIDATE_INT,
        'lot-step' => FILTER_VALIDATE_INT,
        'lot-date' => FILTER_DEFAULT
    ], true);
    $lot_data['owner-id'] = filter_var($id);

    return $lot_data;
}

/**
 * Получает значение поля из отправленной формы
 *
 * @param  string  $name  имя поля формы
 *
 * @return string|null  Значение поле или null, если поле пустое
 */
function get_post_val(string $name): ?string
{
    return filter_input(INPUT_POST, $name);
}

/**
 * Фильтрует значение поискового запроса
 *
 * @param  string  $query  Строка поискового запроса
 *
 * @return string|null  Значение запроса или null, если поле пустое
 */
function get_get_val(string $query): ?string
{
    return filter_input(INPUT_GET, $query);
}

/**
 * Получает текст для сообщения на основе шаблона письма, данных лота и пользователя,
 * выигравшего аукцион
 *
 * @param  array  $winner  имя пользователя
 * @param  int  $lot_id  id лота
 *
 * @return Swift_Message    объект Swift_Message
 */
function get_message(array $winner, int $lot_id): Swift_Message
{
    $msg_content = include_template('email.php', [
        'winner' => $winner,
        'lot_id' => $lot_id
    ]);

    $message = (new Swift_Message())
        ->setSubject('Ваша ставка победила')
        ->setFrom(['keks@phpdemo.ru' => 'Yeticave'])
        ->setTo([$winner['email'] => $winner['user_name']])
        ->setBody($msg_content, 'text/html');

    return $message;
}