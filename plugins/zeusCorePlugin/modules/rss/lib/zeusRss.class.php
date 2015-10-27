<?php

class zeusRss {
  private static $instance = null;
  protected $url = null;
  protected $proxy = null;
  
  public static function getInstance()
  {
    if(!zeusRss::$instance) {
      zeusRss::$instance = new zeusRss;
    }
    
    return zeusRss::$instance;
  }
  
  public function getIncludes()
  {
    if ($this->proxy) {
      $url = $this->proxy->registerLink();
      sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
      $url = $this->url ? $this->url : 'rss/feed';
      
      // <?php echo zeusRss::getInstance()->getIncludes(); 
      return '<link rel="alternate" title="RSS Feed" type="application/rss+xml" href="'.url_for($url).'">'."\n";
    }
  }
  
  public function registerLink($url)
  {
    $this->url = $url;
  }
  
  public function getProxy($proxy = 'Blog')
  {
    if (!$this->proxy) {
      $proxy_object = $proxy.'Proxy';
      // this should be configurable
      $this->proxy = call_user_func_array(array($proxy_object, 'getInstance'), array());
      
      $url = $this->proxy->registerLink();
      sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
      
      $url = $this->url ? $this->url : 'rss/feed';
      
    }
    
    return $this->proxy;
  }
}