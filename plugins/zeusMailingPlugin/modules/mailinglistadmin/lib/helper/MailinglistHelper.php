<?php

function mailinglist_default_template($object, $config = array()) 
{
  $v = $object->getDefaulttemplate();
  if (!$v) $v = 'Default';
  $templates = glob(sfConfig::get('sf_app_dir').'/modules/mailingadmin/templates/_mailing*.php');
  
  $tmp = array();
  foreach ($templates as $template) {
    $f = basename($template);
    $f = substr($f,8);
    $f = substr($f, 0, -4);
    $tmp[$f] = $f;
  }
  
  sfContext::getInstance()->getConfiguration()->loadHelpers(array('Form'));
  return form_row('defaulttemplate', select_tag('defaulttemplate', options_for_select($tmp, $v)), array('label' => 'Standaard template'));
  
}