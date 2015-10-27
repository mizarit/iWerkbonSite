<?php 

$config = zeusYaml::load('admin.yml');

if (isset($config['list'])) {
  use_helper('ZeusList');
  echo zeus_list($config['list']);
}
else {
  throw new sfException('No list section found in admin.yml');
}

include_component('core', 'helpers');
