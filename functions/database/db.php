<?php
function db_connect(array $db): array
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