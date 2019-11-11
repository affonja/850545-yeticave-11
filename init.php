<?php

date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('function.php');
require_once('data.php');
require_once('config/db.php');
//ini_set('display_errors','Off');
$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");
//ini_set('display_errors','On');
$categories = [];
$page_content = '';
