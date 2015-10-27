<?php

class baseSitemapActions extends sfActions
{
  public function executeIndex()
  {
    $response = sfContext::getInstance()->getResponse();
	  $response->addJavascript('/zeusCore/js/zeus-sitemap/zeus-sitemap.js');
	  $response->addStylesheet('/zeusCore/css/zeus-sitemap/zeus-sitemap-screen.css');
	 
	
    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
    
    $root = $this->getRoot();
    
    $tree = $this->recurse($root);
    
    $this->tree = $tree;
  }
 
  private function getRoot()
  {
    $c = new Criteria;
    $c->add(MenuPeer::TREE_PARENT, null, Criteria::ISNULL);
    $root = MenuPeer::doSelectOne($c);
    return $root; 
  }
  
  private function recurse($root)
  {
    $ret = array();
    
    foreach ($root->getChildren() as $child) { 
      
      $v = array(
        'title' => $child->getTitle(),
        'url'   => route_for($child)
      );
      
      if ($child->hasChildren()) {
        $v['children'] = $this->recurse($child);
      }
      
      $ret[] = $v;
    }
    
    return $ret;
  }
  
  public function executeGoogleSitemap()
  {
    
  }
 }