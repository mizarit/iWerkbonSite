<?php

class zeusYaml extends sfYaml
{
  public static function load($input, $module = false)
  {
    if (!$module) {
      $params = sfContext::getInstance()->getRequest()->getParameterHolder();
      $module = $params->get('module');
    }
    
    if (!strpos($input, "\n") || strpos($input, '.yml')) {
      // might be a filename, try to resolve it
      
      // app module dir
      $files = glob(sfConfig::get('sf_app_dir').'/modules/'.$module.'/config/'.$input);
      if($files !== false && count($files)){
        return parent::load($files[0]);
      }
      
      // plugin module dir
      $files = glob(sfConfig::get('sf_plugins_dir').'/*/modules/'.$module.'/config/'.$input);
      if($files !== false && count($files)){
        return parent::load($files[0]);
      }
      
      // any config 
      $files = glob(sfConfig::get('sf_plugins_dir').'/*/modules/*/config/'.$input);
       
      if($files !== false && count($files)){
        return parent::load($files[0]);
      }
    }
    
    return parent::load($input);
  }
}