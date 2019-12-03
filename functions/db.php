<?php
function db_connect(array $db): mysqli
{
    $connection = mysqli_connect($db['host'], $db['user'],
        $db['password'], $db['database']);
    if (!$connection) {
        exit(mysqli_connect_error());
    }

    mysqli_set_charset($connection, "utf8");
    return $connection;
}

function add_lot(mysqli $connection, array $lot): ?int
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
    $stmt = db_get_prepare_stmt($connection, $sql, $lot);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        return mysqli_insert_id($connection);
    }
    return null;
}

function add_user(mysqli $connection, array $user): bool
{
    $password = password_hash($user['password'], PASSWORD_DEFAULT);
    $sql = <<<SQL
INSERT INTO users ( creation_time, email, name, password, contacts) 
VALUES ( NOW(),?,?,?,?)
SQL;
    $stmt = db_get_prepare_stmt($connection, $sql, [
        $user['email'],
        $user['name'],
        $password,
        $user['message']
    ]);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return true;
    }

    return null;
}