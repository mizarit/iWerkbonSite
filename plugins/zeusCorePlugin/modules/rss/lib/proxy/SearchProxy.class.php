<?php

class SearchProxy extends zeusBaseRssProxy
{
  public static function getInstance($params = null)
  {
    if(!self::$instance) {
      self::$instance = new SearchProxy;
    }
    
    if ($params) {
      self::$instance->setParameters($params);
    }
    
    return self::$instance;
  }
  
  public function getItems()
  {
    sfLoader::loadHelpers('ZeusRoute');
    
    $request = sfContext::getInstance()->getRequest();
    
    $query = '';
    if ($request->hasParameter('query')) {
      $query = $request->getParameter('query');
    }
    
    $results = zeusSearch::getInstance()->search($query);
    
    $ret = array();
    
    if ($results) {
      foreach ($results['hits'] as $result) {
        $peer = $result['object'].'Peer';
        $object = call_user_func_array(array($peer, 'retrieveByPk'), array($result['id']));
        if ($object) {
          $ret[] = array(
            'title' => $object->getTitle(),
            'description' => substr(strip_tags($object->getContent()),0,500),
            'link' => route_for($object),
            'pubdate' => strtotime($object->getPdate()),
            'category' => 'category'
          );
        }
      }
    }
   
    return array_slice($ret, 0, 20);
  }
  
  public function getFeedTitle()
  {
    sfLoader::loadHelpers('ZeusRoute');
    
    $request = sfContext::getInstance()->getRequest();
 
    return 'zoekopdracht: '.$this->params['query'];
  }
  
  public function registerLink()
  {
    $url = 'rss/feed?proxy=search';
    if (isset($this->params['query'])) {
      $url .= '&query='.$this->params['query'];
    }
    zeusRss::getInstance()->registerLink($url);
    
    return $url;
  }
}