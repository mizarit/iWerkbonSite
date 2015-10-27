<?php

class NewsProxy extends zeusBaseRssProxy
{
  public static function getInstance($params = null)
  {
    if(!self::$instance) {
      self::$instance = new NewsProxy;
    }
    
    if ($params) {
      self::$instance->setParameters($params);
    }
    
    return self::$instance;
  }
  
  public function getItems()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
    
    $valid_objects = NewsPeer::getValidNews();
  
    $objects = array_slice($valid_objects, 0, 10);
    
    $ret = array();
    
    if ($objects) {
      foreach ($objects as $object) {
    
        $ret[] = array(
          'title' => $object->getTitle(),
          'description' => substr(strip_tags($object->getContent()),0,500),
          'link' => route_for($object),
          'pubdate' => strtotime($object->getDate()),
          'category' => 'category'
        );
      }
    }
   
    return array_slice($ret, 0, 20);
  }
  
  public function getFeedTitle()
  {
    return '';
  }
  
  public function registerLink()
  {
    $url = 'rss/feed?proxy=news';
    zeusRss::getInstance()->registerLink($url);
    
    return $url;
  }
}