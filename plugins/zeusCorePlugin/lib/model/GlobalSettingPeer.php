<?php


/**
 * Skeleton subclass for performing query and update operations on the 'global_setting' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Tue Nov 30 15:19:08 2010
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    plugins.zeusCorePlugin.lib.model
 */
class GlobalSettingPeer extends BaseGlobalSettingPeer {

  public static function findByKey($key, $default = '')
  {
    list($group, $k) = explode('/', $key);
    $c = new Criteria;
    $c->add(GlobalSettingPeer::GGROUP, $group);
    $c->add(GlobalSettingPeer::TITLE, $k);
    $setting = GlobalSettingPeer::doSelectOne($c);
    if (!$setting) {
      $setting = new GlobalSetting;
      $setting->setGgroup($group);
      $setting->setTitle($k);
      $setting->setValue($default);
      $setting->setGtype('input');
    }
    
    return $setting;
  }
} // GlobalSettingPeer