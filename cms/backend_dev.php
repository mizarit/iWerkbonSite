<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '81.204.48.43', '86.90.47.101', '94.210.131.6')))
{
//  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

if (isset($_POST['PHPSESSID'])) {
  session_id($_POST['PHPSESSID']);
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
