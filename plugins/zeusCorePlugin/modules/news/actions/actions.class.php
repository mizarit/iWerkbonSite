<?php

class newsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    
    $url = '/'.$this->getRequestParameter('y').'/'.$this->getRequestParameter('m').'/'.$this->getRequestParameter('p');

    

  
    if (sfConfig::get('sf_i18n')) {
      if(preg_match('/\/[a-z]{2}_[A-Z]{2}/', $url, $ar)) {
        $url = substr($url, 6);
      }
    } 
  
    $c = new Criteria;
    $c->add(RoutePeer::URL, $url);
    $c->add(RoutePeer::OBJECT, 'Menu', Criteria::NOT_EQUAL);
    $route = RoutePeer::doSelectOne($c, Propel::getConnection('news'));
    if ($route) {
      $peer = $route->getObject().'Peer';
      $c->clear();
      $c->add(NewsPeer::ID, $route->getObjectId());
      $c->setLimit(1);
      $object = call_user_func_array(array($peer, 'doSelectWithI18N'), array($c, 'nl_NL', Propel::getConnection('news')));
      $object = array_shift($object);
    }
 
    if(!isset($object)) {
      $this->redirect('@homepage');
    }
    
    $object->setCulture($this->getUser()->getCulture());
    
    sfContext::getInstance()->getResponse()->setTitle($object->getTitle());
    $this->object = $object;
  }
}
