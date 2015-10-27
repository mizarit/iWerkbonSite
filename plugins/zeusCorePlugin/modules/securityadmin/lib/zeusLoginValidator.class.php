<?php

class zeusLoginValidator {

  public static function execute ($validator, $value, $arguments) {

  	list($username, $password, $formatter) = $arguments;

  	$formatter = substr($formatter, 0, strpos($formatter, '['));
  	$request = sfContext::getInstance()->getRequest();
  	
  	$bound_fields = $request->getParameter($formatter);
  	
  	$username = $bound_fields[$username];
  	$password = $bound_fields[$password];
  	
  	
  	//echo hash('sha512', 'jorgen'.'fc6c0c872a2aa18e244ad41ce2481d0a');
  	//exit;
  	
  	$c = new Criteria;
  	$c->add(UserPeer::USERNAME, $username);
  	//$c->add(UserPeer::PASSWORD, md5($password));
  	$users = UserPeer::doSelect($c);
  	$user = false;
  	if ($users) {
  	  foreach ($users as $user) {
  	    if($user->getPassword() == hash('sha512', $password.$user->getSalt())) {
  	      break;
  	    }
  	    $user = false;
  	  }
  	}
  	
  	if ($user) {
  	  $user->setLastlogin($user->getCurrentlogin());
  	  $user->setLastip($user->getIp());
  	  $user->setCurrentlogin(time());
  	  $user->setIp($_SERVER['REMOTE_ADDR']);
  	  $user->save();
  	  
  	  sfContext::getInstance()->getUser()->setAttribute('username', $user->getUsername());
  	  sfContext::getInstance()->getUser()->setAttribute('usertitle', $user->getTitle());
  	  sfContext::getInstance()->getUser()->setAttribute('userid', $user->getId());
  	  
  	  if (isset($bound_fields['remember'])) {
  	    sfContext::getInstance()->getResponse()->setCookie('username', $user->getUsername(), strtotime('+1 year'), '/', $_SERVER['HTTP_HOST']);
  	    sfContext::getInstance()->getResponse()->setCookie('password', $user->getPassword(), strtotime('+1 year'), '/', $_SERVER['HTTP_HOST']);
  	  }
  	  else {
  	    sfContext::getInstance()->getResponse()->setCookie('username', null, strtotime('-1 year'), '/', $_SERVER['HTTP_HOST']);
  	    sfContext::getInstance()->getResponse()->setCookie('password', null, strtotime('-1 year'), '/', $_SERVER['HTTP_HOST']);
  	  }
  	  return $user->getUsername();
  	}
  	
    throw new sfValidatorError($validator, 'invalid', array('value' => $value, 'invalid' => $validator->getOption('invalid')));
  }
}
