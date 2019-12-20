<?php
require_once('init.php');
if (!file_exists('vendor/autoload.php')) {
    exit('Выполнител команду composer install');
}
require_once('vendor/autoload.php');

$lots = get_expired_lots_without_win($connection);
$transport = (new Swift_SmtpTransport(
    $config['mailer']['host'],
    $config['mailer']['port']
))
    ->setUsername($config['mailer']['username'])
    ->setPassword($config['mailer']['password']);

$mailer = new Swift_Mailer($transport);

foreach ($lots as $lot) {
    $winner = get_winner($connection, $lot['id']);
    if (isset($winner['user_id'])) {
        add_winner_to_lot($connection, $lot['id'], $winner['user_id']);
        $message = get_message($winner, $lot['id']);
        $result = $mailer->send($message);
    }
}