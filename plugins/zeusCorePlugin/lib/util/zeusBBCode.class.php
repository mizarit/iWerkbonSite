<?php

class zeusBBCode
{
  public static  public function parse($content) 
  {
    //$content = preg_replace('\b(https?|ftp|file):\/\/[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]', '<a href="\$0">\$0</a>', $content);
    return $content;
  }
}