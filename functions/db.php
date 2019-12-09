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

function get_categories(mysqli $connection): array
{
    $sql = 'SELECT id, name, code FROM categories';
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        exit(mysqli_error($connection));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

function get_active_lots(mysqli $connection): array
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

    if (!$result) {
        exit(mysqli_error($connection));
    }

    return $result = mysqli_fetch_all($result, MYSQLI_ASSOC) ?? [];
}

function get_lot(mysqli $connection, int $id): array
{
    $sql = <<<SQL
            SELECT
            l.id, l.name, l.img, l.description,
            l.bet_start, l.bet_step,
            l.creation_time, l.end_time,
            c.name AS category,
            MAX(b.sum) as max_bet
            FROM lots l
            INNER JOIN categories c ON category_id = c.id
            LEFT JOIN bets b ON b.lot_id = l.id
            WHERE l.id =?
            GROUP BY l.id
SQL;

    $stmt = db_get_prepare_stmt($connection, $sql, [$id]);
    if (!mysqli_stmt_execute($stmt)) {
        exit(mysqli_error($connection));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit(mysqli_errno($connection));
    }
    $lot = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!empty($lot)) {
        $lot['min_next_bet'] = ($lot['max_bet'] ?? $lot['bet_start'])
            + $lot['bet_step'];
    }

    return $lot ?? [];
}

function get_email(mysqli $connection, $email): bool
{
    $sql = 'SELECT email FROM users WHERE email = ?';
    $stmt = db_get_prepare_stmt($connection, $sql, [$email]);
    if (!mysqli_stmt_execute($stmt)) {
        exit(mysqli_errno($connection));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        return true;
    }

    return false;
}

function get_pass(mysqli $connection, string $email): array
{
    $sql = "SELECT password FROM users WHERE email = ?";
    $stmp = db_get_prepare_stmt($connection, $sql, [$email]);
    if (!mysqli_stmt_execute($stmp)) {
        exit(mysqli_errno($connection));
    }
    $result = mysqli_stmt_get_result($stmp);
    $pass = mysqli_fetch_array($result, MYSQLI_ASSOC);

    return $pass;
}

function get_user(mysqli $connection, string $email): array
{
    $sql = "SELECT id, name FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        exit(mysqli_error($connection));
    }

    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $user;
}

function get_lots_count(mysqli $connection, string $query)
{
    if ($query) {
        $sql = <<<SQL
SELECT COUNT(*) as count_item
FROM lots
WHERE end_time > NOW()
  AND MATCH(name, description) AGAINST(? IN BOOLEAN MODE)
SQL;
        $stmt = db_get_prepare_stmt($connection, $sql, [$query]);
        if (!mysqli_stmt_execute($stmt)) {
            exit(mysqli_error($connection));
        }

        $result = mysqli_stmt_get_result($stmt);
        $lots_found = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($lots_found['count_item'] > 0) {
            return $lots_found['count_item'];
        }

        return 'Ничего не найдено по вашему запросу';
    }

    return 'пустой запрос';
}

function get_searching_lots(
    mysqli $connection,
    string $query,
    int $limit,
    int $offset
): array {
    $sql = <<<SQL
SELECT l.id,
       l.name,
       bet_start,
       img,
       end_time,
       creation_time,
       c.name AS category
FROM lots l
         INNER JOIN categories c on l.category_id = c.id
WHERE end_time > NOW()
  AND MATCH(l.name, description) AGAINST(? IN BOOLEAN MODE)
ORDER BY creation_time DESC
LIMIT $limit OFFSET $offset
SQL;
    $stmt = db_get_prepare_stmt($connection, $sql, [$query]);
    if (!mysqli_stmt_execute($stmt)) {
        exit(mysqli_error($connection));
    }

    $result = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $lots;
}

function get_add_bet(
    mysqli $connection,
    int $bet,
    int $lot_id,
    int $user_id
): bool {
    $sql = <<<SQL
	INSERT INTO bets
	SET 
	    creation_time = NOW(),
		sum = ?,
		user_id = ?,
		lot_id = ?
SQL;
    $stmt = db_get_prepare_stmt($connection, $sql, [
        $bet,
        $user_id,
        $lot_id
    ]);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

function get_bets_for_lot(mysqli $connection, int $lot_id): array
{
    $sql = <<<SQL
SELECT name, sum, b.creation_time FROM bets b 
INNER JOIN users u ON b.user_id=u.id
WHERE lot_id=?
ORDER BY creation_time DESC 
SQL;
    $bets = get_bets($connection, $sql, $lot_id);

    return $bets ?? [];
}

function get_bets_for_user(mysqli $connection, int $user_id): array
{

    $sql = <<<SQL
SELECT l.img,
       l.name,
       l.winner_id,
       c.name AS category,
       l.end_time,
       b.lot_id,
       b.sum,
       b.creation_time,
       b.win
FROM bets b
         INNER JOIN lots l ON b.lot_id = l.id
         INNER JOIN categories c ON l.category_id = c.id
WHERE b.user_id = ?
ORDER BY b.creation_time DESC
SQL;
    $bets = get_bets($connection, $sql, $user_id);

    return $bets ?? [];
}

function get_bets(mysqli $connection, string $sql, int $id): array
{
    $stmp = db_get_prepare_stmt($connection, $sql, [$id]);
    mysqli_stmt_execute($stmp);
    $result = mysqli_stmt_get_result($stmp);
    if (!$result) {
        exit(mysqli_error($connection));
    }

    $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($bets as &$bet) {
        $bet['time_back'] = get_bet_timeback($bet['creation_time']);
    }

    return $bets;
}