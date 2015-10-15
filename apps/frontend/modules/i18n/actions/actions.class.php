<?php

class i18nActions extends sfActions
{
  public function executeSwitch()
  {
    $cultures = sfConfig::get('sf_enabled_cultures');
    
    $current_culture = $this->getUser()->getCulture();
    $new_culture = $this->getRequestParameter('culture');
    
    if (in_array($new_culture, $cultures)) {
      $this->getUser()->setCulture($new_culture);
    }
    
    $referer = $_SERVER['HTTP_REFERER'];
    $referer = str_replace($current_culture, $new_culture, $referer);
    if ($referer =='') {
      $referer = '@homepage';
    }
    $this->redirect($referer);
  }
}