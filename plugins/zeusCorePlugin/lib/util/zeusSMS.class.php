<?php

class zeusSMS
{
  public static function send($phone, $message)
  {
    $app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
    $cfg = $app['all']['sms'];

    $batchId = self::getBatchId($message);

    $sendTest = false;

    //Collect data
    $data = array(
      'VERSION' => '1.1',
      'UID'     => $cfg['username'],
      'PWD'     => $cfg['password'],
      'N'       => self::formatNumber($phone),
      'O'       => $cfg['name'],
      'M'       => substr($message,0,160),
      'BATCHID' => $batchId,
      'NOT'     => 1,    
      'TEST'    => $sendTest ? 1 : 0,
    );
    //Set special options for numeric originators to prevent problems with Apple iPhone
    //if($message->numericSender()){
      $data['ONUM'] = 1;
    //}
    //Configurate delayed delivery
    /*if(isset($options['deliveryDate']) && $options['deliveryDate']){
        if(is_numeric($options['deliveryDate'])){
            $data['DATE'] = date("Y-m-d H:i:s", $options['deliveryDate']);
        } else {
            $data['DATE'] = date("Y-m-d H:i:s", strtotime($options['deliveryDate']));
        }
    }*/
    $response = self::sendRequest($cfg['url'], $data);
    //Parse response: it should be of the form 001=something
    if(preg_match('/^([0-9]{3})=(.*)$/', $response, $matches)){
      $code = $matches[1];
      $result = $matches[2];
      if($code[0]=='0'){
        //OK
        return true;
      } else {
        echo false;
      }
    } else {
      //Incorrect return format
      echo false;
    }
  }
  
  protected static function formatNumber($number)
  {
    $number = trim($number);
    $number = str_replace(' ', '', $number);
    $number = str_replace('-', '', $number);
    if (strlen($number) == 10 && $number[0] == 0) {
      $number = '+31'.substr($number, 1, 9);
    }
    else if ($number[0] == '+') {
      
    }
    return $number;
  }
  
  protected static function sendRequest($url, $data= array()){
    $content = http_build_query($data);
    $params = $params = array('http' => array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => $content
    ));
    $stream = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $stream);
    $response = false;
    if ($fp) {
      $response = @stream_get_contents($fp);
    }
    return $response;
  }
    
  protected static function getBatchId($message){
    return substr(time().md5($message),1,29).'PV12';
  }
}