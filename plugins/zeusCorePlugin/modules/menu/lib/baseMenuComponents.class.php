<?php

class baseMenuComponents extends sfComponents
{
  public function executeDefault()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('FrontMenu');
    
    if (!isset($this->root)) {
      $this->root = $this->getRoot();
      $active = $this->recurseActive($this->root);
      $active_urls = array();
      
      if ($this->getRequest()->hasParameter('force_active_url')) {
        $active_urls[] = $this->getRequest()->getParameter('force_active_url');
      }
      
      if ($active) {
        $active_urls[] = frontmenu_get_url($active);
        
        while($active = $active->getParent()) {
          $active_urls[] = frontmenu_get_url($active);
        }
      }
      
      if ($_SERVER['REQUEST_URI'] == '/') {
        $active_urls[] = '/nl_NL/home';
      }
      
      if ($_SERVER['REQUEST_URI'] == '/frontend_dev.php') {
        $active_urls[] = '/frontend_dev.php/nl_NL/home';
      }
      
      $this->active_urls = $active_urls;
    }
    
  }
  
  public function executeSubnav()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('FrontMenu');
    
    //if (!isset($this->root)) {
      $this->root = $this->getRoot();
      $active = $this->recurseActive($this->root);
      $active_urls = array();
      
      if ($this->getRequest()->hasParameter('force_active_url')) {
        $active_urls[] = $this->getRequest()->getParameter('force_active_url');
      }
      
      if ($active) {
        $active_urls[] = frontmenu_get_url($active);
        
        while($active = $active->getParent()) {
          $active_urls[] = frontmenu_get_url($active);
        }
      }
      
      if ($_SERVER['REQUEST_URI'] == '/') {
        $active_urls[] = '/nl_NL/home';
      }
      
      if ($_SERVER['REQUEST_URI'] == '/frontend_dev.php') {
        $active_urls[] = '/frontend_dev.php/nl_NL/home';
      }
      
      $this->active_urls = $active_urls;
   // }
    $active = false;
    foreach ($this->root->getChildren() as $child) {
      $r = route_for($child);
      $r = str_replace('/nl_NL/contact', '/formulier/contact', $r);
      if (in_array($r, $active_urls)) {
        $this->root = $child;
        $active = true;
      }
    }
    
    if (!$active) {
      $this->root = false;
    }
  }
  
  private function getRoot()
  {
    $c = new Criteria;
    if (isset($this->root_id)) {
      $root = MenuPeer::retrieveByPk($this->root_id);
    }
    else {
      $c->add(MenuPeer::TREE_PARENT, null, Criteria::ISNULL);
      $root = MenuPeer::doSelectOne($c);
    }
    return $root; 
  }
  
  private function recurseActive($root)
  {
    $env = sfConfig::get('sf_environment') == 'dev' ? '/frontend_dev.php' : '';
    
    foreach ($root->getChildren() as $child) { 
      $url = frontmenu_get_url($child);
      
     //$url = '';
     
      $active = $this->recurseActive($child);
      if ($active) {
        return $active;
      }
      
      $active = $url == $_SERVER['REQUEST_URI'] || $url == $env.'/home' && $_SERVER['REQUEST_URI'].'/' == url_for('@homepage');
      
      if ($active) {
        return $child;
      }
      
      
    }
  }
}