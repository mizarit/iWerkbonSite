<?php

class baseCaptchaActions extends sfActions
{
  public function executeImage()
  {

    $size = 0.7;
    
    $code = (string) rand(1000, 9999);
    
    $this->getUser()->setAttribute('captcha-'.$this->getRequestParameter('f'), md5($code));

    $im = imagecreatetruecolor(round(100*$size), round(30*$size));

    $white = imagecolorallocate($im, 255, 255, 255);
    $grey = imagecolorallocate($im, 127, 127, 127);
    $black = imagecolorallocate($im, 0, 0, 0);

    imagefilledrectangle($im, 0, 0, 200, round(35*$size), $white);

    $fonts = glob(dirname(__FILE__).'/../data/fonts/*');
    
    for ($i = 0; $i < 4; $i++) {
      $char = $code[$i];
      $font = $fonts[rand(0, count($fonts)-1)];
      $offset = $i * 20;
      $angle = rand(-20, 20);
      imagettftext($im, round(22*$size), $angle, round(($offset + 14)*$size), round(26*$size), $grey, $font, $char);
    
      imagettftext($im, round(22*$size), $angle, round(($offset + 10)*$size), round(22*$size), $black, $font, $char);
    }
    
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    
    $this->im = $im;
  }
}