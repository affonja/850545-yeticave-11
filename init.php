<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("Europe/Moscow");

$config = require 'config/config.php';
require_once('functions/helpers.php');
require_once('functions/getters.php');
require_once('functions/db.php');
require_once('functions/validation.php');
require_once('functions/other.php');

require_once('data.php');

$connection = db_connect($config['db']);

$error = '';
$categories = [];
$page_content = '';





