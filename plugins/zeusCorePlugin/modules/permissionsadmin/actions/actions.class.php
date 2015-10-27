<?php

class permissionsadminActions extends zeusActions 
{
  protected $model = 'User';
  
  public function executeIndex(sfWebRequest $request)
  {
    if ($this->getRequest()->getMethod() == 'POST') {
      foreach ($_POST as $key => $value) {
        list($k, $v) = explode('-', $key);
        $setting = GlobalSettingPeer::retrieveByPk($v);
        if ($setting) {
          $setting->setValue($value);
          $setting->save();
        }
      }
      sfContext::getInstance()->getRequest()->setParameter('message',  'De wijzigingen zijn opgeslagen.');
    }
  }
  public function executeConfig()
  {
    
  }
}