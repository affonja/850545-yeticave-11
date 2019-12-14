<?php

function get_lot_form_data($lot_data): array
{
    $lot_data = filter_var_array($lot_data, [
        'lot-name' => FILTER_DEFAULT,
        'category' => FILTER_DEFAULT,
        'message'  => FILTER_DEFAULT,
        'lot-rate' => FILTER_VALIDATE_INT,
        'lot-step' => FILTER_VALIDATE_INT,
        'lot-date' => FILTER_DEFAULT
    ], true);

    return $lot_data;
}

function get_post_val(string $name): ?string
{
    return filter_input(INPUT_POST, $name);
}

function get_get_val(string $query): ?string
{
    return filter_input(INPUT_GET, $query);
}

//function get_user_form_reg_data(array $user_data): array
//{
//    $user_data = filter_var_array($user_data, [
//        'email'    => FILTER_DEFAULT,
//        'password' => FILTER_DEFAULT,
//        'name'     => FILTER_DEFAULT,
//        'message'  => FILTER_DEFAULT
//    ], true);
//
//    return $user_data;
//}

//function get_user_form_login_data(array $user_data): array
//{
//    $user_data = filter_var_array($user_data, [
//        'email'    => FILTER_VALIDATE_EMAIL,
//        'password' => FILTER_DEFAULT
//    ], true);
//
//    return $user_data;
//}

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