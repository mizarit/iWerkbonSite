<?php

class zeusPropelBehavior extends sfPropelBehavior 
{
  private static $registred_behaviors;
  
  static public function add($class, $behaviors)
  {
    self::$registred_behaviors[$class] = $behaviors;
    return parent::add($class, $behaviors);
  }
  
  static public function hasBehavior($class, $behavior)
  {
    if (is_object($class)) {
      if (method_exists($class, 'getRawValue')) {
        $class = get_class($class->getRawValue());
      }
      else {
        $class = get_class($class);
      }
    }
    
    if (isset(self::$registred_behaviors[$class])) {
      if (in_array($behavior, self::$registred_behaviors[$class])) {
        return true;
      }
    }
    
    return false;
  }
}