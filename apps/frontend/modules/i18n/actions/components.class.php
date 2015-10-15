<?php

class i18nComponents extends sfComponents
{
  public function executeLanguageswitch()
  {
    $this->cultures = sfConfig::get('sf_enabled_cultures');
    $this->current_culture = $this->getUser()->getCulture();
  }
}