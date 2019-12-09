<?php

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
        $time_diff
    ];

    return $time_remaining;
}

function price_format(int $price): string
{
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, '.', ' ');
    }

    return $price;
}

function save_file(array $lot_img): string
{
    $file_name = $lot_img['name'];
    $ext = substr($file_name, strrpos($file_name, '.'));
    $link = '/uploads/'.uniqid().$ext;
    move_uploaded_file($lot_img['tmp_name'],
        substr($link, 1));

    return $link;
}

function get_bet_state(array $bet, int $user_id): array
{
    $time_remaining = get_time_remaining($bet['end_time']);
    $bet_state = [
        'state'   => '',
        'message' => sprintf("%02d", $time_remaining[0]).':'
            .sprintf("%02d", $time_remaining[1]),
        'class'   => ''
    ];

    if ($time_remaining[2] === 0) {
        $bet_state['state'] = 'timer--end';
        $bet_state['message'] = 'Торги окончены';
        $bet_state['class'] = 'rates__item--end';
        if ($bet['winner_id'] === $user_id AND $bet['win'] === 1) {
            $bet_state['state'] = 'timer--win';
            $bet_state['message'] = 'Ставка выиграла';
            $bet_state['class'] = 'rates__item--win';
        }
    } elseif ($time_remaining[0] < 1) {
        $bet_state['state'] = 'timer--finishing';
    };

    return $bet_state;
}

function get_bet_timeback(string $bets_creation_time): string
{

    $now = time();
    $bet_time = strtotime($bets_creation_time);
    $diff_time = $now - $bet_time;
    if ($diff_time < 59) {
        $time_back = $diff_time.' '.get_noun_plural_form($diff_time,
                'секунда', 'секунды', 'секунд').' назад';
    } elseif ($diff_time < 3600) {
        $diff_time = floor($diff_time / 60);
        $time_back = $diff_time.' '.get_noun_plural_form($diff_time,
                'минута', 'минуты', 'минут').' назад';
    } elseif ($diff_time < 86400) {
        $diff_time = floor($diff_time / 3600);
        $time_back = $diff_time.' '.get_noun_plural_form($diff_time,
                'час', 'часа', 'часов').' назад';
    } elseif ($diff_time > 86400) {
        $time_back = date('d.m.y в H:i', $bet_time);
    }

    return $time_back;
}