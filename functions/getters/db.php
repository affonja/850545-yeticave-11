<?php

function get_сategories(mysqli $connection, string &$error): ?array
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