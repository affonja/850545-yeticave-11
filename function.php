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

date_default_timezone_set("Europe/Moscow");

function lot_lifetime(string $lot_time): array
{
    $time_now = time();
    $time_end = strtotime($lot_time);
    $time_diff = $time_end - $time_now;

    $time_remaining = [
        floor($time_diff / 3600),
        floor(($time_diff % 3600) / 60),
    ];

    return $time_remaining;
}
