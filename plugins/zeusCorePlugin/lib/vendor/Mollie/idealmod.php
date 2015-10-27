<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
require_once(dirname(__FILE__).'../../../inc/settings.inc.php');
require_once(dirname(__FILE__).'/classes/zeusMollie.class.php');
require_once(dirname(__FILE__).'/classes/zeusIdeal.class.php');

$ideal = new zeusIdeal(320595);
exit;
