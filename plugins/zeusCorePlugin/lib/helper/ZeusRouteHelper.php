<?php

function route_for($object, $return_url = true, $connection = null)
{
  if (is_object($object) || strpos($object, ':')) {
    
    if (is_object($object) && get_class($object) == 'sfOutputEscaperObjectDecorator') {
      $object = $object->getRawValue();
    }
    
    if (is_string($object)) {
      list($object_object, $object_id) = explode(':', $object);
      $object = call_user_func_array(array($object_object.'Peer', 'retrieveByPk'),array($object_id, $connection));
    }
    
    else {
      $object_object = get_class($object);
      $object_id = $object->getId();
    }
    
    static $cache = array();
    if (count($cache) == 0) {
      $routes = RoutePeer::doSelect(new Criteria);
      foreach ($routes as $route) {
        $cache[$route->getObject().':'.$route->getObjectId()] = $route->getUrl();
      }
    }
    
    if ($return_url && isset($cache[$object_object.':'.$object_id])) {
      $url = $cache[$object_object.':'.$object_id];
      if (sfConfig::get('sf_environment') == 'dev') {
        $url = '/'.sfConfig::get('sf_app').'_dev.php'.$url;
      }
      return $url;
    }
    
    $c = new Criteria;
    $c->add(RoutePeer::OBJECT, $object_object);
    $c->add(RoutePeer::OBJECT_ID, $object_id);
    
    $route = RoutePeer::doSelectOne($c, $connection);
    
    // check if route is not the default route
    if ($route && $object) {
      if (method_exists($object, 'getUrlFormat')) {
        $url = $object->getUrlFormat('');
        
        if ($url == $route->getUrl()) {
          // the url is the default url
          $route->delete();
          $route = false;
        }
      }
    }
    
    if (!$route) {
      $route = new Route;
      $route->setObject($object_object);
      $route->setObjectId($object_id);
      
      if ($object) {
        $url = zeusTools::smartUrl($object->getTitle());
        
        if (method_exists($object, 'getUrlFormat')) {
          $url = $object->getUrlFormat($url);
        }
        else {
          $url = '/'.$url;
        }
      }
      else {
        $url = '';
      }
      
      // check if the route already exists
      $exists = true;
      $starturl = $url;
      $count = 0;
      while($exists && $count < 10) {
        $count++;
        $url = $starturl.'-'.$count;
        
        $c->clear();
        $c->add(RoutePeer::URL, $url);
        $check = RoutePeer::doSelectOne($c);
        
        if (!$check) {
          $exists = false;
        }
      }
      
      if ($count == 1) {
        $url = $starturl;
      }
      
      $route->setUrl($url);
      $route->save();
    }
    
    if ($return_url) {
      $url = $route->getUrl();
    
      $ret = '';
      
      if (sfConfig::get('sf_environment') == 'dev') {
        $ret .= '/frontend_dev.php';
      }
      
      if (sfConfig::get('sf_i18n')) {
        //$ret .= '/'.sfContext::getInstance()->getUser()->getCulture();
      }
      
      $ret .= $url;
      
      $cache[$object_object.':'.$object_id] = $ret;
      
      return $ret;
    }
    
    return $route;
  }
  
  // fallback behavior
  return url_for($object);
}

function object_for($url = null, $connection = 'propel')
{
  if (!$url) {
    $url = str_replace('/frontend_dev.php', '', $_SERVER['REQUEST_URI']);
  }
  
  if (sfConfig::get('sf_i18n')) {
    if(preg_match('/\/[a-z]{2}_[A-Z]{2}/', $url, $ar)) {
      $url = substr($url, 6);
    }
  } 
  
  if (strpos($url, '?')) {
    $url = substr($url, 0, strpos($url, '?'));
  }
  
  if ($url == '') $url = '/home';
  
  $c = new Criteria;
  $c->add(RoutePeer::URL, $url);
  $c->add(RoutePeer::OBJECT, 'Menu', Criteria::NOT_EQUAL);
  $route = RoutePeer::doSelectOne($c, Propel::getConnection($connection));
  if ($route) {
    $peer = $route->getObject().'Peer';
    return call_user_func_array(array($peer, 'retrieveByPk'), array($route->getObjectId(), Propel::getConnection($connection)));
  }
  
  if (strpos($url, ':')) {
    list($object, $object_id) = explode(':', $url);
    $object = call_user_func_array(array($object.'Peer', 'retrieveByPk'), array($object_id, Propel::getConnection($connection)));
    // store it, if nessecary
    $route = route_for($object);
    return $object;
  }
}

function object_for_request()
{
  return object_for();
}

function remove_route_for($object)
{
  $route = route_for($object, false);
  $route->delete();
}