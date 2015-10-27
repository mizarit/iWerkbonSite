<?php

class basePageActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $p = $this->getRequestParameter('p');

    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
    $url = '/'.$p;
    
    if ($url == '/home') {
      $this->setLayout('layout-home');
    }
    
    if (sfConfig::get('sf_i18n')) {
      if (strpos($this->getUser()->getCulture(), '_')) {
        $url = '/'.$this->getUser()->getCulture().$url;
      }
    }
    
    $object = object_for($url);
    
    if ($object && method_exists($object, 'getHtmlTitle')) {
      sfContext::getInstance()->getResponse()->setTitle($object->getHtmltitle());
    }
    
    $this->forward404Unless($object);
   
    if ($object) {
      $culture = $object->getCulture();
      
      $object->setViews($object->getViews() + 1);
      $object->save();
      $object->setCulture($culture);
      $this->page = $object;
    }
  }
}