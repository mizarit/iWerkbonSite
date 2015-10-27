<?php

class coreComponents extends sfComponents 
{
  public function executeHelpers()
  {
    
  }
  
  public function executeFrontend()
  {
    $code = zeusConfig::get('Google Analytics', 'Google Analytics account', 'input', 'UA-XXXXXXXX-X');
    
    $this->code = $code;
  }
}