<?php

function validate_lot_form(array $lot_data, $file_data, array $cat_ids): array
{
    $errors = [];
    $errors['lot-name'] = validate_lot_name($lot_data['lot-name']);
    $errors['category'] = validate_lot_category($lot_data['category'],
        $cat_ids);
    $errors['message'] = validate_lot_message($lot_data['message'], 3, 3000);
    $errors['lot-rate'] = validate_lot_rate($lot_data['lot-rate']);
    $errors['lot-step'] = validate_lot_step($lot_data['lot-step']);
    $errors['lot-date'] = validate_lot_date($lot_data['lot-date'], 'P1D');
    $errors['file'] = validate_lot_file($file_data);
    $errors = array_filter($errors);

    return $errors;
}

function validate_lot_name(string $name): ?string
{
    if (!$name) {
        return 'Имя лота не может быть пустым';
    }
    if (mb_strlen($name) < 3) {
        return 'Название не менее 3 символов';
    }
    if (mb_strlen($name) >= 128) {
        return 'Название не более 50 символов';
    }

    return null;
}

function validate_lot_category(string $category, array $category_list): ?string
{
    if (!in_array($category, $category_list)) {
        return "Не выбрана категория";
    }

    return null;
}

function validate_lot_message(string $message, int $min, int $max): ?string
{
    if (!$message) {
        return 'Заполните описание';
    }
    $len = mb_strlen($message);
    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

function validate_lot_rate(int $rate): ?string
{
    if (!$rate or $rate < 0) {
        return 'Число должно быть больше 0';
    }

    return null;
}

function validate_lot_step(int $step): ?string
{
    if (!$step or $step < 0) {
        return 'Шаг ставки должен быть больше 0';
    }

    return null;
}

function validate_lot_date(string $date, string $interval): ?string
{
    if (!$date) {
        return 'Выберите дату';
    }
    $now = new DateTime();
    $min_interval = new DateInterval($interval);
    $min_date = date_format(date_add($now, $min_interval), 'Y-m-d');
    if ($date <= $min_date) {
        return 'Дата должна быть больше текущей хотя бы на 1 день';
    }

    return null;
}

function validate_lot_file(array $file_data): ?string
{
    if ($file_data['size'] === 0) {
        return $error = 'Не загружен файл';
    }
    $path = $file_data['tmp_name'];
    $file_type = mime_content_type($path);
    $allow_type = [
        'image/png',
        'image/jpeg'
    ];
    if (!in_array($file_type, $allow_type)) {
        return $error = 'Неверный формат файла';
    }

    return null;
}

function validate_reg_form(mysqli $connection, array $user_data): array
{
    $errors = [];
    $errors['email'] = validate_email($connection, $user_data['email']);
    $errors['password'] = validate_pass($user_data['password']);
    $errors['name'] = validate_user_name($user_data['name']);
    $errors['message'] = validate_contacts($user_data['message'], 3, 3000);
    $errors = array_filter($errors);

    return $errors;
}

function validate_email(mysqli $connection, $email): ?string
{
    if (!$email) {
        return 'Заполните поле';
    }
    $email_is_double = get_email($connection, $email);
    if ($email_is_double) {
        return 'Такой email уже зарегистрирован';
    }

    return null;
}

function validate_pass(string $pass): ?string
{
    if (!$pass) {
        return 'Заполните поле';
    } elseif (mb_strlen($pass) < 6) {
        return 'Слишком короткий пароль';
    }

    return null;
}

function validate_user_name(string $name): ?string
{
    if (!$name) {
        return 'Имя пользователя не может быть пустым';
    }
    if (mb_strlen($name) < 3) {
        return 'Имя не менее 3 символов';
    }
    if (mb_strlen($name) >= 60) {
        return 'Имя не более 60 символов';
    }

    return null;
}

function validate_contacts(string $message, int $min, int $max): ?string
{
    if (!$message) {
        return 'Заполните контакты';
    }
    $len = mb_strlen($message);
    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

function validate_login_form(mysqli $connection, array $user_data): array
{
    $errors = [];
    $errors['email'] = validate_email_exist($connection, $user_data['email']);
    if (empty($errors['email'])){
        $errors['password'] = validate_email_pass($connection, $user_data['email'],
            $user_data['password']);
    }
    $errors = array_filter($errors);

    return $errors;
}

function validate_email_exist(mysqli $connection, string $email): ?string
{
    if (!$email) {
        return 'Заполните поле';
    }
    $email_exist = get_email($connection, $email);
    if (!$email_exist) {
        return 'Такой email не зарегистрирован';
    }

    return null;
}

function validate_email_pass(
    mysqli $connection,
    string $email,
    string $pass
): ?string {
    if (!$pass) {
        return 'Заполните поле';
    }
    $pass_from_bd = get_pass($connection, $email);

    if (!password_verify($pass, $pass_from_bd['password'])) {
        return 'Неверный пароль';
    }

    return null;
}