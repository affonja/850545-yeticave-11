<?php
require_once('init.php');

$lots = get_lots_without_win($connection);
foreach ($lots as &$lot){
  $lot['winner_id'] = get_winner($connection, $lot['lot_id']) ?? null;
}
unset($lot);

foreach ($lots as $lot){
    if ($lot['winner_id']){
        add_winner_to_lot($connection, $lot['lot_id'], $lot['winner_id']);
    }
}
//send_mail_to_winner();
