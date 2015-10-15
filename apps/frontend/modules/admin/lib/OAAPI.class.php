<?php

class OAAPI {
  public function __construct()
  {
    $credentials_id = sfContext::getInstance()->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);

    $company = CompanyPeer::retrieveByPK($credentials->getCompanyId());

    $connection = $company->getConnection();
    //var_dump($current_user->xid);
    //var_dump($current_user->getApiServer());
    //exit;
    if (!defined('API_SECRET')) {
      define('API_URL', $connection->getApiServer());
      define('API_KEY', $connection->getApiKey());
      define('API_SECRET', $connection->getApiSecret());
    }
  }
  /**
   *
   * Sends an API request to the OnlineAfspraken.nl REST server
   *
   * @param string $method
   * @param array $parameters
   *
   * @return array $records
   */
  public function sendRequest($method, $parameters = array())
  {
    $url = $this->createRequestURL($method, $parameters);

    $response = @file_get_contents($url);
    //echo $url." \n";
    //echo $response;
    if (!$response) {
      return false;
      //$this->throwException('Could not load API URL', 'B'.__LINE__);
    }
    $xml = simplexml_load_string($response);
    if ($xml->Status->Status == 'failed') {
      $this->error = $xml->Status->Message;
      return false;
      //$this->throwException($xml->Status->Message, $xml->Status->Code);
    }

    $records = array();

    $records['Debug']['Url'] = $url;
    //$records['Debug']['Response'] = $response;

    if ($xml->Status->Stats) {
      $records['Stats'] = $xml->Status->Stats;
    }

    if ($xml->Objects) {
      foreach ($xml->Objects[0] as $k => $object) {
        foreach ($object as $key => $attributes) {
          $record[$key] = (string)$attributes;
        }

        $records[$k][] = $record;
      }
      $records['Stats'] = $xml->Status->Stats;
    }
    else {
      foreach ($xml as $k => $object) {
        if ($k == 'Status') {
          $records['Debug']['Status'] = $object;
          continue;
        }

        foreach ($object as $key => $attributes) {
          $record[$key] = (string)$attributes;
        }

        if (isset($record)) {
          $records[$k][] = $record;
        }
      }
    }

    return $records;
  }

  /**
   *
   * Throws an exception when things go terribly wrong
   *
   * @param string $message
   * @param string $code
   */
  public function throwException($message, $code)
  {
    echo 'ERROR '.$message.' Code '.$code;
    exit;
  }

  /**
   *
   * Creates a valid API call URL, based on a method with parameters
   *
   * @param string $method
   * @param array $parameters
   *
   * @return string $API_REST_url;
   */
  public function createRequestURL($method, $parameters = array())
  {
    $salt = time();

    $signature = $this->sign(array_merge(array('method'=>$method), $parameters), API_SECRET, $salt);

    $url = API_URL.'?api_salt='.$salt.'&api_signature='.$signature.'&api_key='.API_KEY.'&method='.$method;
    foreach ($parameters as $key => $value) {
      $url .= '&'.urlencode($key).'='.urlencode($value);
    }

    __log($url);

    return $url;
  }

  /**
   *
   * Signs a set of parameters
   *
   * @param array $params, associative array with parameters, like AgendaId=>1, etc.
   * @param string $api_secret, see the API settings screen for this value
   * @param string $api_salt, ususally the timestamp
   *
   * @return string $signature
   */
  public function sign($params, $api_secret, $api_salt)
  {
    ksort($params);
    $sign_str = '';
    foreach ($params as $key => $value) {
      $sign_str .= str_replace(' ', '_', $key).$value;
    }

    $sign_str .= $api_secret.$api_salt;

    __log('sign string '.$sign_str);

    return sha1(str_replace(' ', '', $sign_str));
  }
}

/**
 *
 * Logs a mesage, and optionally dumps it to Firebug
 *
 * @param string $string
 * @param boolean $dump
 */
function __log($string, $dump = false)
{
  static $logs = array();
  $logs[] = $string;
  if ($dump) {
    echo '<script type="text/javascript">'.PHP_EOL;
    echo '/* <![CDATA[ */'.PHP_EOL;
    foreach ($logs as $log) {
      if (trim($log) == '') continue;
      echo "console.log('".addslashes($log)."');".PHP_EOL;
    }
    echo '/* ]]> */'.PHP_EOL;
    echo '</script>'.PHP_EOL;
  }
}