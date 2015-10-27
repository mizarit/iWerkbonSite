<?php
class multisiteActions extends sfActions
{
  public function executeDirectlogin()
  {
    $username = $this->getRequestParameter('username');
    
    $c = new Criteria;
    $c->add(UserPeer::USERNAME, $username);
    $user = UserPeer::doSelectOne($c);
    
    $this->forward404Unless($user);
    
    $password = $user->getPassword();
    
    $salt = $this->getRequestParameter('salt');
    $secret = zeusConfig::get('Multisite', 'Salt', 'input', 'secret');
    
    $hash_test = md5($username.$password.$salt.$secret);
    
    if ($hash_test == $this->getRequestParameter('hash')) {
      
      $user->setLastlogin($user->getCurrentlogin());
  	  $user->setLastip($user->getIp());
  	  $user->setCurrentlogin(time());
  	  $user->setIp($_SERVER['REMOTE_ADDR']);
  	  $user->save();
  	  
  	  sfContext::getInstance()->getUser()->setAttribute('username', $user->getUsername());
  	  sfContext::getInstance()->getUser()->setAttribute('usertitle', $user->getTitle());
  	  sfContext::getInstance()->getUser()->setAttribute('userid', $user->getId());
  	  
  	  
      $this->getUser()->setAttribute('username', $username);
      $this->getUser()->setAuthenticated(true);
      $this->redirect('@homepage');
    }
    
    $this->redirect('@homepage');
  }
}