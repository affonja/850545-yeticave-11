<?php
$time_remaining = get_time_remaining($lot['end_time']);
$timer = [
    'state'   => '',
    'message' =>
        sprintf("%02d", $time_remaining['h']).':'
        .sprintf("%02d", $time_remaining['m']),
    'class'   => ''
];

if ($time_remaining['diff'] === 0) {
    $timer['state'] = 'timer--end';
    $timer['message'] = 'Торги окончены';
    $timer['class'] = 'rates__item--end';
    if (isset($lot['bet_id']) and in_array($lot['bet_id'], $win_bets)) {
        $timer['state'] = 'timer--win';
        $timer['message'] = 'Ставка выиграла';
        $timer['class'] = 'rates__item--win';
    }
} elseif ($time_remaining['diff'] < 3600) {
    $timer['state'] = 'timer--finishing';
}

if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
    $classname_templ = 'lot__timer ';
} elseif ($_SERVER['SCRIPT_NAME'] == '/lot.php') {
    $classname_templ = 'lot-item__timer';
}
?>

<div class="<?= $classname_templ; ?> timer <?= $timer['state']; ?>">
    <?= $timer['message']; ?>
</div>
