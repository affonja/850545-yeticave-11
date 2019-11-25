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