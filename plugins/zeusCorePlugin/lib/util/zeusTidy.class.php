<?php

class zeusTidy
{
  public static function parse($html)
  {
    $html = str_replace('<br />', '<br>', $html);
    $html = str_replace('target="_blank"', 'rel="external"', $html);
    
    $html = htmlspecialchars_decode($html);
    $html = html_entity_decode($html);
    
    return utf8_encode($html);
  }
}