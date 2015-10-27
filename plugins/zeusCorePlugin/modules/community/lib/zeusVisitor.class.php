<?php

class zeusVisitor
{
  private static $instance = null;
  private $visitor = null;
  
  /**
   * @return zeusVisitor
   */
  public static function getInstance($return_url = false)
  {

    if ($return_url) {
      sfContext::getInstance()->getUser()->setAttribute('visitor_return_url', $return_url);
    }
    
    if (zeusVisitor::$instance) {
      return zeusVisitor::$instance;
    }
    
    zeusVisitor::$instance = new zeusVisitor;
    
    return zeusVisitor::$instance;
  }
  
  public function isLoggedIn()
  {
    $loggedin = sfContext::getInstance()->getUser()->getAttribute('visitor_loggedin');
    
    if ($loggedin && !$this->visitor) {
      $this->visitor = VisitorPeer::retrieveByPk(sfContext::getInstance()->getUser()->getAttribute('visitor_id'));
    }
    
    return $loggedin;
  }
  
  public function doLogin($visitor)
  {
    $this->visitor = $visitor;
    $visitor->setLastIp($_SERVER['REMOTE_ADDR']);
    $visitor->save();
    
    sfContext::getInstance()->getUser()->setAttribute('visitor_loggedin', true);
    sfContext::getInstance()->getUser()->setAttribute('visitor_id', $visitor->getId());
    
    $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    $return_url = sfContext::getInstance()->getUser()->getAttribute('visitor_return_url');
    if (!$return_url) {
      $return_url = '@homepage';
    }
    $action->redirect($return_url);
  }
  
  public function doLogoff()
  {
    sfContext::getInstance()->getUser()->setAttribute('visitor_loggedin', false);
    $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    $return_url = sfContext::getInstance()->getUser()->getAttribute('visitor_return_url');
    if (!$return_url) {
      $return_url = '@homepage';
    }
    $action->redirect($return_url);
  }
  
  public function getName()
  {
    if ($this->isLoggedIn()) {
      return $this->visitor->getName();
    }
  }
  
  public function getVisitor()
  {
    if ($this->isLoggedIn()) {
      return $this->visitor;
    }
  }
}