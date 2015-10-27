<?php

class zeusFavorites
{
  private static $favs = array();
  
  public static function register($name, $actionurl)
  {
    self::$favs[] = array($name, $actionurl);
  }
  
  public static function get()
  {
    return self::$favs;
  }
}