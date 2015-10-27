<?php

class zeusCaptcha
{
  private $field = '';
  
  private function setField($field)
  {
    $this->field = $field;
  }
  public static function getInstance($field = 'default')
  {
    $ret = new zeusCaptcha();
    $ret->setField($field);
    return $ret;
  }
  
  public function __toString()
  {
    sfLoader::loadHelpers('Url');
    return '<input class="captcha" type="text" name="captcha-'.$this->field.'" id="captcha-'.$this->field.'"> <img src="'.url_for('captcha/image?f='.$this->field).'" alt="" class="captcha-image" id="captcha-'.$this->field.'-image">';
  }
  
  public function check()
  {
    $value = sfContext::getInstance()->getRequest()->getParameter('captcha-'.$this->field);
    $code = sfContext::getInstance()->getUser()->getAttribute('captcha-'.$this->field);
    
    sfContext::getInstance()->getUser()->setAttribute('captcha-'.$this->field, null);
    return $code == md5($value);
  }
}