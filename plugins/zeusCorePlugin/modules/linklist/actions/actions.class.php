<?php

class linklistActions extends sfActions
{
  public function executeJson()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('ZeusRoute', 'Url'));
    $c = new Criteria;
    $c->addAscendingOrderByColumn(PageI18NPeer::TITLE);
    $pages = PagePeer::doSelectWithI18N($c, 'nl_NL');
    foreach ($pages as $page) {
      $value = 'Page:'.$page->getId();
      $title = $page->getTitle();
      $list[route_for($value)] = $title;
    }

    $actions = sfConfig::get('app_actionlinks_list');
    if ($actions) {
      foreach ($actions as $action => $label) {
        $list[route_for($action)] = 'Actie: '.$label;
      }
    }
    
    foreach ($list as $url => $value) {
      $url = str_replace('/frontend_dev.php', '', $url);
      $url = str_replace('/backend_dev.php', '', $url);
      
      $list_valid[$url] = $value;
    }
    
    $files = glob(sfConfig::get('sf_upload_dir').'/*');
    foreach ($files as $file) {
      if (is_dir($file)) continue;
      $parts = explode('.', $file);
      $ext = strtolower(array_pop($parts));
      if (!in_array($ext, array('jpg', 'png', 'gif', 'jpeg', 'bmp'))) {
        $fn = basename($file);
        $list_valid['/uploads/'.$fn] = 'Bestand: '.$fn;
      }
    }
    
    $list = $list_valid;
    
    $this->list = $list;
  }
}