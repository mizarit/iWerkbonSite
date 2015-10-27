<?php

class useradminActions extends zeusActions 
{
  protected $model = 'User';
  
  public function preSave($object)
  {
    if ($this->getRequestParameter('password') != '') {
      $object->setPassword(md5($this->getRequestParameter('password')));
    }
  }
}