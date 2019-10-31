<?php

function price_format(int $price): string
{
    $price = ceil($price);
    if ($price >= 1000) {
        $price = number_format($price, 0, '.', ' ');
    }
    $price .= ' <b class="rub">р</b>';
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