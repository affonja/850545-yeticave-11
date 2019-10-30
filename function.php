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
