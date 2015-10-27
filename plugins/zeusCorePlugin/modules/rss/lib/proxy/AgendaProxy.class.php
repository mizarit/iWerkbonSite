<?php

class AgendaProxy extends zeusBaseRssProxy
{
  public static function getInstance($params = null)
  {
    if(!self::$instance) {
      self::$instance = new AgendaProxy;
    }
    
    if ($params) {
      self::$instance->setParameters($params);
    }
    
    return self::$instance;
  }
  
  public function getItems()
  {
    sfLoader::loadHelpers('ZeusRoute');
    
    $c = new Criteria;
    $c->add(AgendaPeer::EDATE, time(), Criteria::GREATER_EQUAL);
    //$c->setLimit(10);
    $c->addAscendingOrderByColumn(AgendaPeer::EDATE);
    $c->setLimit(10);
    $objects = AgendaPeer::doSelect($c);
    
    $ret = array();
    
    if ($objects) {
      foreach ($objects as $object) {
    
        $ret[] = array(
          'title' => $object->getTitle(),
          'description' => substr(strip_tags($object->getContent()),0,500),
          'link' => route_for($object),
          'pubdate' => strtotime($object->getEdate()),
          'category' => 'category'
        );
      }
    }
   
    return array_slice($ret, 0, 20);
  }
  
  public function getFeedTitle()
  {
    return 'agenda';
  }
  
  public function registerLink()
  {
    $url = 'rss/feed?proxy=agenda';
    zeusRss::getInstance()->registerLink($url);
    
    return $url;
  }
}