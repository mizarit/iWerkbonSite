<?php

class pvCrumblepath {
  
  static $path = array();
  
  public static function add($url, $title)
  {
    if (is_object($url)) {
      $url = get_class($url).':'.$url->getId();
    }
    self::$path[$url] = $title; 
  }
  
  public static function get()
  {
    return self::$path;
  }
}