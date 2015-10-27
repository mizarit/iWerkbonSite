<?php

class zeusWURFL
{
  public static function getInstance()
  {
    static $wurflManager = false;
    
    if (!$wurflManager) {
      define("WURFL_DIR", dirname(__FILE__) . '/../vendor/WURFL/');
      define("RESOURCES_DIR", dirname(__FILE__) . "/../vendor/WURFL/resources/");
  
      set_time_limit(300);
      
      require_once WURFL_DIR . 'Application.php';
      
      // Uncomment the follwoing lines to use the xml configuration file
      $wurflConfigFile = RESOURCES_DIR . 'wurfl-config.xml';
      $wurflConfig = new WURFL_Configuration_XmlConfig($wurflConfigFile);
      
      $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
      
      $wurflManager = $wurflManagerFactory->create(true);
    }
    
    return $wurflManager;
  }
  
  public static function getDevice()
  {
    $wurfl = zeusWURFL::getInstance();
    return $wurfl->getDeviceForHttpRequest($_SERVER);
  }
  
  public static function getInfo()
  {
    $wurfl = zeusWURFL::getInstance();
    return $wurfl->getWURFLInfo();
  }
  
  public static function isMobileDevice()
  {
    $device = zeusWURFL::getDevice(); 
    if ($device->getCapability('resolution_width') <= 480 || $device->getCapability('resolution_height') <= 480)
    {
      return true;
    }
    
    return false;
  }
  
}