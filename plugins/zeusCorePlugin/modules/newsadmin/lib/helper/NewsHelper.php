<?php

function news_list_shown($object = null, $config = array())
{
  return NewsPeer::isValidNews($object, false) && $object->getSites() ? 'Ja' : 'Nee';
}

function news_list_sites($object = null, $config = array())
{
  $sites = explode('|', $object->getSites());
  $allsites = sfConfig::get('app_multisite_cms');
  $tmp = array();
  foreach ($allsites as $key => $site) {
    if (in_array($key, $sites)) {
      $tmp[] = $site['title'];
    }
  }
  
  if (count($tmp) == count($allsites)) {
    return 'Alle websites';
  }
  
  return implode(', ', $tmp);
}

function news_item_shown($object, $config = array())
{
  return NewsPeer::isValidNews($object);
}

function news_edit_sites($object, $config = array())
{
  $sites = sfConfig::get('app_multisite_cms');
  if (count($sites) > 1) {
    $ret = '';
    
    $selected = explode('|', $object->getSites());
    foreach ($sites as $key => $site) {
      $id = 'site_'.$key;
      $checked = in_array($key, $selected) ? ' checked="checked"' : '';
      $ret .= '<input '.$checked.' class="checkbox" type="checkbox" id="'.$id.'" name="'.$id.'"> <label for="'.$id.'">'.$site['title'].'</label> ';
    }
    
    return form_row('sites', $ret, $config);
  }
}

function news_list_import($object, $config = array())
{
   zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Importeren uit RSS', 
    'icon'  => 'fileimport',
    'type'  => 'large', 
    'id'    => 'import-news-btn',
    'callback' => "window.location.href='".url_for('newsadmin/importfeeds')."'"
  )), 'Import');
}