<?php
date_default_timezone_set("Europe/Moscow");
$config = require 'config/config.php';
require_once('helpers.php');
require_once('function.php');
require_once('data.php');

$connection = dbConnect($config['db']);

$categories = [];
$page_content = '';





