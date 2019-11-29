<?php

function get_categories(mysqli $connection, string &$error): ?array
{
    $sql = 'SELECT id, name, code FROM categories';
    $result = mysqli_query($connection, $sql);
    $error = mysqli_error($connection);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return null;
}

function get_active_lots(mysqli $connection, string &$error): ?array
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
    GROUP BY l.id, l.creation_time
    ORDER BY l.creation_time DESC LIMIT 6
SQL;
    $result = mysqli_query($connection, $sql);
    $error = mysqli_error($connection);

    if ($result) {
        return $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return null;
}

function get_lot(mysqli $connection, int $id): ?array
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
            WHERE l.id =?
            GROUP BY l.id
SQL;

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt,
        'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $lot = null;
    if ($result) {
        $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    return $lot;
}

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

function get_user_form_reg_data(array $user_data): array
{
    $user_data = filter_var_array($user_data, [
        'email'    => FILTER_VALIDATE_EMAIL,
        'password' => FILTER_DEFAULT,
        'name'     => FILTER_DEFAULT,
        'message'  => FILTER_DEFAULT
    ], true);

    return $user_data;
}

function get_email(mysqli $connection, $email): bool
{
    $sql = 'SELECT email FROM users WHERE email = ?';
    $stmt = db_get_prepare_stmt($connection, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        return true;
    }

    return false;
}

function get_user_form_login_data(array $user_data): array
{
    $user_data = filter_var_array($user_data, [
        'email'    => FILTER_VALIDATE_EMAIL,
        'password' => FILTER_DEFAULT
    ], true);

    return $user_data;
}

function get_pass(mysqli $connection, string $email): array
{
    $sql = "SELECT password FROM users WHERE email = ?";
    $stmp = db_get_prepare_stmt($connection, $sql, [$email]);
    mysqli_stmt_execute($stmp);
    $result = mysqli_stmt_get_result($stmp);
    $pass = mysqli_fetch_array($result, MYSQLI_ASSOC);

    return $pass;
}

function get_user(mysqli $connection, string $email): array
{
    $sql = "SELECT id, name FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $user;
}
