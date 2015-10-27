<?php

class zeusSearchIndexerCustomer extends zeusSearchIndexerBase 
{
  protected $fields = array(
    'title' => array('type' => 'keyword'),
    'content' => array('type' => 'unstored')
  );
  
}