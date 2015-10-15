<?php

class multisiteComponents extends sfComponents
{
  public function executeSubnav()
  {
    $app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
    $this->services = ($app['all']['services']);
  }
  
  public function executeBlocks()
  {
    $app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
    $this->services = ($app['all']['services']);
    $this->language = str_replace('de', 'du', substr($this->getUser()->getCulture(), 0, 2));
  }
  
  public function executeNewsticker()
  {
    $valid_objects = array();
    $unique = 0;
    
   // $objects = NewsPeer::doSelect(new Criteria, Propel::getConnection('ticker'));
    $objects = NewsPeer::doSelectWithI18N(new Criteria, $this->getUser()->getCulture(), Propel::getConnection('ticker'));
    
    foreach ($objects as $object) { 
      
      $shown = true;
      if ($object->getDate() != '') {
        // at least we have a startdate
        if (time() < strtotime($object->getDate())) {
          $shown = false;
        }
      }
      
      if ($object->getEnddate() != '') {
        // at least we have an enddate
        if (time() > strtotime($object->getEnddate())) {
          $shown = false;
        }
      }
      
      if ($shown) {
  
        // so time is valid, now check if the news item is visible, on ticker and on this site
        if ($object->getVisible()) {
          if ($object->getTicker()) {
            $sites = explode('|', $object->getSites());
            if (!$sites) $sites = array();
            
            if (in_array($this->site, $sites)) {
              // valid, so add it to our display list
              $unique++;
              $valid_objects[$object->getDate().$unique] = $object;
            }
          }
        }
      }
    }
    
    ksort($valid_objects);
    
    $this->objects = $valid_objects;
  }
}