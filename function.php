<?php
function price_format(int $price): string
{
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, '.', ' ');
    }
    return $price;
}

function get_time_remaining(string $time): array
{
    $time_now = time();
    $time_end = strtotime($time);
    $time_diff = $time_end - $time_now;
    if ($time_diff < 0) {
        $time_diff = 0;
    }

    $time_remaining = [
        floor($time_diff / 3600),
        floor(($time_diff % 3600) / 60),
    ];

    return $time_remaining;
}

function dbConnect(array $db): array
{
    $connection = [
        'link'  => '',
        'error' => ''
    ];

    $connection['link'] = mysqli_connect($db['host'], $db['user'],
        $db['password'], $db['database']);
    if (!$connection['link']) {
        $connection['error'] = mysqli_connect_error();
    } else {
        mysqli_set_charset($connection['link'], "utf8");
    }
    return $connection;
}

function getCategories(mysqli $connection, string &$error): ?array
{
    $sql = 'SELECT id, name, code FROM categories';
    $result = mysqli_query($connection, $sql);
    $error = mysqli_error($connection);

    if ($result) {
        return $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return null;
}

function getActiveLots(mysqli $connection, string &$error): ?array
{

    $sql = <<<SQL
	SELECT
		l.id, l.name, l.bet_start,
		l.img, l.end_time,
		c.name AS category
	FROM lots l
	INNER JOIN categories c ON l.category_id = c.id
	LEFT JOIN bets b ON l.id = b.lot_id
	WHERE l.end_time > NOW()
    GROUP BY l.id
	ORDER BY l.creation_time DESC LIMIT 6
SQL;
    $result = mysqli_query($connection, $sql);
    $error = mysqli_error($connection);

    if ($result) {
        return $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return null;
}

function getLot(mysqli $connection, int $id): ?array
{
    $sql = <<<SQL
SELECT
		l.id, l.name, l.img, l.description,
        l.bet_start, l.bet_step,
		l.creation_time, l.end_time,
		c.name AS category,
		MAX(b.sum) as maxbet
FROM lots l
INNER JOIN categories c ON category_id = c.id
LEFT JOIN bets b ON b.lot_id = l.id
WHERE l.id = ?
    GROUP BY l.id
SQL;

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $lot = null;
    if ($result) {
        $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    return $lot;
}

function validateCategory($value, array $category_list): ?string
{
    if (!in_array($value, $category_list)) {
        return "Не выбрана категория";
    }
    return null;
}

function validateLength($value, int $min, int $max): ?string
{
    if ($value) {
        $len = strlen($value);
        if ($len < $min or $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }
    return null;
}

function validatePrice($value): ?string
{
    if ($value) {
        if (!is_float($value) or ($value < 0)) {
            return 'Некорректное число';
        }
    }
    return null;
}

function validateBetStep($value): ?string
{
    if ($value) {
        if (!is_int($value) or ($value < 0)) {
            return 'Некорректное число';
        }
    }
    return null;
}

function is_interval_valid($value, string $interval): bool
{
    $now = new DateTime();
    $min_interval = new DateInterval($interval);
    $min_date = date_format(date_add($now, $min_interval), 'Y-m-d');
    if ($value >= $min_date) {
        return true;
    }
    return false;
}

function getValidateForm(
    array &$post_arr,
    array $rules,
    array $errors,
    array $required
): array {
    foreach ($post_arr as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }

        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Заполните поле";
        }
    }

    return $errors;
}

function getValidateFile(array &$post_arr): ?string
{
    if ($_FILES['lot_img']['name']) {
        $path = $_FILES['lot_img']['tmp_name'];
        $file_type = mime_content_type($path);
        $allow_type = [
            'image/png',
            'image/jpeg'
        ];

        if (!in_array($file_type, $allow_type)) {
            return $error = 'Неверный формат файла';
        } else {
            $file_name = $_FILES['lot_img']['name'];
            $ext = substr($file_name, strrpos($file_name, '.'));
            $file_name = uniqid().$ext;

            $post_arr['img'] = '/uploads/'.$file_name;
            move_uploaded_file($_FILES['lot_img']['tmp_name'],
                substr($post_arr['img'], 1));
        }
    } else {
        return $error = 'Не загружен файл';
    }
    return null;
}

function getAddLot(mysqli $connection, array $lot): bool
{
    $sql = <<<SQL
INSERT INTO lots (
name, category_id, description,
bet_start, bet_step, end_time,
img, creation_time,  owner_id )
VALUES (
       ?,?,?,?,?,?,?,NOW(),1
)
SQL;
    $lot['lot-rate'] *= 100;
    $lot['lot-step'] *= 100;
    $stmt = db_get_prepare_stmt($connection, $sql, $lot);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        return true;
    }
    return null;
}

function getPostVal(string $name): ?string
{
    return filter_input(INPUT_POST, $name);
}

function validateEmail($value, mysqli $connection): ?string
{
    if ($value) {
        $sql = 'SELECT email FROM users WHERE email = ?';
        $stmt = db_get_prepare_stmt($connection, $sql, [$value]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            return 'Такой email уже зарегистрирован';
        }
    }
    return null;
}

function validatePass($value): ?string
{
    if ($value) {
        if (strlen($value) < 6) {
            return 'Не менее 6 символов';
        }
    }
    return null;
}

function getAddUser(mysqli $connection, array $user): bool
{
    $password = password_hash($user['password'], PASSWORD_DEFAULT);
    $sql = <<<SQL
INSERT INTO users ( creation_time, email, name, password, contacts) 
VALUES ( NOW(),?,?,?,?)
SQL;
    $stmt = db_get_prepare_stmt($connection, $sql, [
        $user['email'],
        $password,
        $user['name'],
        $user['password']
    ]);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return true;
    }
    return null;
}

function getFormData(array $form_data):array {
    $form_data = filter_input_array(INPUT_POST, [
            'lot-name' => FILTER_DEFAULT,
            'category' => FILTER_DEFAULT,
            'message'  => FILTER_DEFAULT,
            'lot-rate' => FILTER_VALIDATE_FLOAT,
            'lot-step' => FILTER_VALIDATE_INT,
            'email' => FILTER_VALIDATE_EMAIL,
            'lot-date' => FILTER_DEFAULT,
            'password' => FILTER_DEFAULT
    ], false);
    return $form_data;
}

function validateEmail2($value, mysqli $connection): ?string
{
    if ($value) {
        $sql = 'SELECT email FROM users WHERE email = ?';
        $stmt = db_get_prepare_stmt($connection, $sql, [$value]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_get_result($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            return 'Такой email не зарегистрирован';
        }
    }
    return null;
}