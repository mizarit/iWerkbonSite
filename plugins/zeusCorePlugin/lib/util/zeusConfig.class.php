<?php

class zeusConfig
{
  public static function get($group, $key, $type, $default_value, $extra = '')
  {
    $c = new Criteria;
    $c->add(GlobalSettingPeer::GGROUP, $group);
    $c->add(GlobalSettingPeer::TITLE, $key);
    
    $setting = GlobalSettingPeer::doSelectOne($c);
    if ($setting) {
      return $setting->getValue();
    }
    
    $setting = new GlobalSetting;
    $setting->setGgroup($group);
    $setting->setTitle($key);
    $setting->setGtype($type);
    $setting->setValue($default_value);
    $setting->setExtra($extra);
    $setting->save();
    return $default_value;
  }
}