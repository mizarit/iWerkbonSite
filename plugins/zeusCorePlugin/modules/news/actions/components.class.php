<?php

class newsComponents extends sfComponents
{
  public function executeShortlist()
  {
   
    $valid_objects = NewsPeer::getValidNews();
  
    $items = array_slice($valid_objects, 0, $this->items);

    $this->items = $items;
    
    zeusRss::getInstance()->getProxy('News');
  }
}