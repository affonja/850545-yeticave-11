<?php

/**
 * Валидирует данные полученные из формы
 *
 * @param  array  $lot_data  Массив с данными из формы
 * @param  array  $file_data  Массив данных загруженного файла
 * @param  array  $cat_ids  Массив с существующими категориями товаров
 *
 * @return array    Массив с текстом ошибок валидации
 */
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

/**
 * Проверяет полученную строку на критерии:
 *  - существующее значение
 *  - минимальная и максимальная длина
 *
 * @param  string  $name  проверяемая строка
 *
 * @return string|null  Текст ошибки или null, если строка соответствует критериям
 */
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

/**
 * Проверяет наличие id категории в массиве существующих категорий
 *
 * @param  string  $category  id проверяемой категории
 * @param  array  $category_list  массив id существующих категорий
 *
 * @return string|null  Текст ошибки или null, если id найден в массиве категорий
 */
function validate_lot_category(string $category, array $category_list): ?string
{
    if (!in_array($category, $category_list)) {
        return "Не выбрана категория";
    }

    return null;
}

/**
 * Проверяет значение полученной строки на критерии:
 *  -   существующее значение
 *  -   минимальная и максимальная длина
 *
 * @param  string  $message  Проверяемая строка
 * @param  int  $min  минимальное значение длины строки
 * @param  int  $max  максимальное значение длины строки
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 * Проверяет значение полученной суммы на критерии:
 *  -   значение больше 0
 *  -   значение заполненно
 *
 * @param  int  $rate  проверяемое число
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
function validate_lot_rate(int $rate): ?string
{
    if (!$rate or $rate < 0) {
        return 'Число должно быть больше 0';
    }

    return null;
}

/**
 * Проверяет значение полученной суммы на критерии:
 *  -   значение больше 0
 *  -   значение заполненно
 *
 * @param  int  $step  проверяемое число
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
function validate_lot_step(int $step): ?string
{
    if (!$step or $step < 0) {
        return 'Шаг ставки должен быть больше 0';
    }

    return null;
}

/**
 * Проверяет значение полученной даты на критерии:
 *  -   значение заполненно
 *  -   дата больше текущей не меньше чем на $interval
 *
 * @param  string  $date  проверяемая дата
 * @param  string  $interval  минимальный интервал
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 * Проверяет загруженный файл на критерии:
 *  -   файл загружен
 *  -   соответствие файла допустимым MIME-форматам
 *      (допустимые форматы в массиве $allow_type)
 *
 * @param  array  $file_data  массив с данными загруженного файла
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 *Валидирует данные полученные из формы регистрации пользователя
 *
 * @param  mysqli  $connection  Ресурс соединения
 * @param  array  $user_data  Данные из формы
 *
 * @return array  Массив с текстом ошибок валидации
 */
function validate_reg_form(mysqli $connection, array &$user_data): array
{
    $errors = [];
    $errors['email'] = validate_email($connection, $user_data['email']);
    $errors['password'] = validate_pass($user_data['password']);
    $errors['name'] = validate_user_name($user_data['name']);
    $errors['message'] = validate_contacts($user_data['message'], 3, 3000);
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Проверяет строку с полученным email на соответствие критериям:
 * -    поле заполнено
 * -    email соответствует формату email
 * -    email не существует в базе данных
 *
 * @param  mysqli  $connection  Ресурс соединения
 * @param  string  $email  Проверяемый email
 *
 * @return string|null Текст ошибки или null, если соответствует критериям
 */
function validate_email(mysqli $connection, string &$email): ?string
{
    if (empty($email)) {
        return 'Заполните поле';
    } else {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return 'Некорректный email';
        }
    }

    $email_is_double = get_email($connection, $email);
    if ($email_is_double) {
        return 'Такой email уже зарегистрирован';
    }

    return null;
}

/**
 * Проверяет строку на соответствие критериям:
 * -    поле заполнено
 * -    длинна не меннее 6 символов
 *
 * @param  string  $pass  Проверяемая строка
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
function validate_pass(string $pass): ?string
{
    if (!$pass) {
        return 'Заполните поле';
    } elseif (mb_strlen($pass) < 6) {
        return 'Слишком короткий пароль. Минимум 6 символов';
    }

    return null;
}

/**
 * Проверяет имя пользователя на на соответствие критериям:
 *  -   поле заполнено
 *  -   минимальная и максимальная длина строки
 *
 * @param  string  $name  Имя пользователя
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 * Проверяет строку на на соответствие критериям:
 *  -   поле заполнено
 *  -   минимальная и максимальная длина строки
 *
 * @param  string  $message  Проверяемая строка
 * @param  int  $min  Минимальная длина строки
 * @param  int  $max  Максимальная длина строки
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 * Валидирует данные полученные из формы входа
 *
 * @param  mysqli  $connection  Ресурс соединения
 * @param  array  $user_data  Данные из формы
 *
 * @return array     Массив с текстом ошибок валидации
 */
function validate_login_form(mysqli $connection, array &$user_data): array
{
    $errors = [];
    $errors['email'] = validate_email_exist($connection, $user_data['email']);
    if (empty($errors['email'])) {
        $errors['password'] = validate_email_pass($connection,
            $user_data['email'],
            $user_data['password']);
    }
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Проверяет строку с полученным email на соответствие критериям:
 * -    поле заполнено
 * -    email соответствует формату email
 * -    email существует в базе данных
 *
 * @param  mysqli  $connection  Ресурс соединения
 * @param  string  $email  Проверяемый email
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
function validate_email_exist(mysqli $connection, string &$email): ?string
{
    if (empty($email)) {
        return 'Заполните поле';
    } else {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return 'Некорректный email';
        }
    }

    $email_exist = get_email($connection, $email);
    if (!$email_exist) {
        return 'Такой email не зарегистрирован';
    }

    return null;
}

/**
 * Проверяет полученный из формы пароль на соответствие в базе данных
 *
 * @param  mysqli  $connection  Ресурс соединения
 * @param  string  $email  email пользователя
 * @param  string  $pass  Проверяемый пароль
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
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

/**
 * Валидирует данные полученные при добавлении новой ставки на критерии:
 *  -   значение заполнено
 *  -   сумма ставки больше минимально допустимой
 *  -   пользователь не является владельцем лота
 *  -   пользователь отличается от владельца последней ставки
 *
 * @param  string  $bet  Сумма текущей ставки
 * @param  int  $min_bet  Минимально допустимая сумма ставки
 * @param  int  $owner  Владелец лота
 * @param  int  $last_better  Пользователь, сделавший последнюю ставку
 * @param  int  $user  Пользователь, сделавший текущую ставку
 *
 * @return string|null  Текст ошибки или null, если соответствует критериям
 */
function validate_bet_form(
    string $bet,
    int $min_bet,
    int $owner,
    int $last_better,
    int $user = 0
): ?string {
    if (!$bet) {
        return $error_bet = 'Введите сумму ставки';
    } elseif ($bet < $min_bet) {
        return $error_bet = 'Введите корректную сумму';
    } elseif (
        $user === $owner or
        $user === $last_better
    ) {
        return $error_bet = 'Вы не можете сделать ставку';
    }

    return null;
}

/**
 * Проверяет наличие id категории в массиве существующих категорий
 *
 * @param  int  $id  id проверяемой категории
 * @param  array  $category  массив id существующих категорий
 *
 * @return bool  true если id найден в массиве категорийб иначе false
 */
function validate_id_category(int $id, array $category): bool
{
    if (!in_array($id, $category)) {
        return false;
    }

    return true;
}