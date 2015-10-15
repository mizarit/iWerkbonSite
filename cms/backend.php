<?php

if (isset($_POST['PHPSESSID'])) {
  session_id($_POST['PHPSESSID']);
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
