<?php

class zeusReplies
{
  private $object = null;
  private $replies = null;
  
  private static $instance = null;
  
  public static function getInstance($object)
  {
    if (!self::$instance) {
      $ret = new zeusReplies;
      $ret->setObject($object);
      self::$instance = $ret;
    }
    return self::$instance;
  }
  
  private function setObject($object)
  {
    $this->object = $object;
  }
  
  public function __toString() {
    //return 'woot';
  }
  
  public function getCount() {
    $this->getReplies();
    
    return count($this->replies);
  }
  
  public function getReplies()
  {
    if (!$this->replies) {
      $c = new Criteria;
      $object = get_class($this->object);
      $c->add(ReplyPeer::OBJECT, $object);
      $c->add(ReplyPeer::OBJECT_ID, $this->object->getId());
      $c->addAscendingOrderByColumn(ReplyPeer::PDATE);
      $this->replies = ReplyPeer::doSelect($c);
    }
    
    return $this->replies;
  }
  
  public static function format($message)
  {
    $message = zeusBBCode::parse($message);
    return $message;
  }
}