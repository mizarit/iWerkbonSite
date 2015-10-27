<?php

class zeusBaseRssProxy
{
  protected static $instance = null;
  public $params = array();

  public static function getInstance($params = null)
  {
    if(!self::$instance) {
      self::$instance = new JobsProxy;
    }
    
    if ($params) {
      self::$instance->setParameters($params);
    }
    
    return self::$instance;
  }
  
  public function getItems()
  {
    return array();
  }
  
  public function getFeedTitle()
  {
    return 'Nieuws';
  }
  
  
  public function setParameters($params = array())
  {
    $this->params = $params;
  }
  
  public function registerLink()
  {
    $url = 'rss/feed?proxy=jobs';
    if (isset($this->params['category'])) {
      $url .= '&category='.$this->params['category'];
    }
    zeusRss::getInstance()->registerLink($url);
  }
}