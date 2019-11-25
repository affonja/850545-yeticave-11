<?php

function get_lot_form_data(array $form_data): array
{
    $form_data = filter_input_array(INPUT_POST, [
        'lot-name' => FILTER_DEFAULT,
        'category' => FILTER_DEFAULT,
        'message'  => FILTER_DEFAULT,
        'lot-rate' => FILTER_VALIDATE_INT,
        'lot-step' => FILTER_VALIDATE_INT,
        'lot-date' => FILTER_DEFAULT
    ], true);
    return $form_data;
}

function get_post_val(string $name): ?string
{
    return filter_input(INPUT_POST, $name);
}

