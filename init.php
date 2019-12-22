<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!file_exists('config/config.php')) {
    exit('Создайте файл config/config.php на основе config.sample.php и сконфигурируйте его');
}
$config = require 'config/config.php';
require_once('functions/helpers.php');
require_once('functions/getters.php');
require_once('functions/db.php');
require_once('functions/validation.php');
require_once('functions/other.php');

$connection = db_connect($config['db']);
$categories = get_categories($connection);

$error = [];
$page_content = '';