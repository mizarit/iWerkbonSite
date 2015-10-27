<?php

class zeusSearchIndexerNews extends zeusSearchIndexerBase 
{
  protected $fields = array(
    'title' => array('type' => 'keyword'),
    'content' => array('type' => 'unstored')
  );
  
}