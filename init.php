<?php
date_default_timezone_set("Europe/Moscow");
$config = require 'config/config.php';
require_once('helpers.php');
require_once('functions/getters/db.php');
require_once('functions/getters/lot.php');
require_once('functions/database/db.php');
require_once('functions/validators/lot.php');
require_once('functions/function.php');

require_once('data.php');

$connection = db_connect($config['db']);

$error = '';
$categories = [];
$page_content = '';





