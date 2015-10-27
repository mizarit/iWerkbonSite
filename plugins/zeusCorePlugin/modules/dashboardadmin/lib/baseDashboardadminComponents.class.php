<?php

class baseDashboardadminComponents extends sfComponents
{
  public function executeLastlogin()
  {
    $user_id = $this->getUser()->getAttribute('userid');
    $user = UserPeer::retrieveByPk($user_id);
    $this->user = $user;
  }
  
  public function executeNews()
  {
    $c = new Criteria;
    $c->add(NewsPeer::VISIBLE, 1);
    $c->addDescendingOrderByColumn(NewsPeer::ID);
    $news = NewsPeer::doSelectWithI18n($c, 'nl_NL', Propel::getConnection('support'));
    $news = array_pop($news);
    $this->news = $news;
    
    $c->clear();
    $c->add(RoutePeer::OBJECT, 'News');
    $c->add(RoutePeer::OBJECT_ID, $news->getId());
    $route = RoutePeer::doSelectOne($c, Propel::getConnection('support'));
    
    $this->route = $route;
  }
  
  public function executeHistory()
  {
    $c = new Criteria;
    $c->addDescendingOrderByColumn(ChangelogPeer::ID);
    $objects = ChangelogPeer::doSelect($c);
    
    $changes = array();
    $tmp = array();
    foreach ($objects as $object) {
      if (count($changes) == 5) break;
      
      if (!isset($tmp[$object->getObject().':'.$object->getObjectId()])) {
        $changes[] = $object;
      }
      
      $test = call_user_func_array(array($object->getObject().'Peer', 'retrieveByPk'), array($object->getObjectId()));
      if (!$test) {
        // Make sure we have no edit urls on non-existing objects
        $object->setActionurl('');
      }
      
      $tmp[$object->getObject().':'.$object->getObjectId()] = true;
    }
    
    $this->changes = $changes;
  }
  
  public function executeFavorites()
  {
    $c = new Criteria;
    $c->addAscendingOrderByColumn(FavoritePeer::TITLE);
    $favorites = FavoritePeer::doSelect($c);
    $this->favorites = $favorites;
  }
  
  public function executeScraps()
  {
    $user = UserPeer::retrieveByPk($this->getUser()->getAttribute('userid'));
    $this->user = $user;
  }
  
  public function executeForms()
  {
    $c = new Criteria;
    $c->addDescendingOrderByColumn(FormdataPeer::DATE);
    $c->setLimit(5);
    $forms = FormdataPeer::doSelect($c);
    $this->forms = $forms;
  }
  
  public function executeSupport()
  {
    $support_userid = zeusConfig::get('zeusSupport', 'Gebruiker ID', 'input', 1);
    $support_username = zeusConfig::get('zeusSupport', 'Gebruikersnaam', 'input', 'username');
    $support_password = zeusConfig::get('zeusSupport', 'Wachtwoord', 'input', 'password');
    
    $c = new Criteria;
    $c->add(SupportReplyPeer::SUPPORT_USER_ID, $support_userid);
    
    $replies = SupportReplyPeer::doSelect(new Criteria, Propel::getConnection('support'));
    $tickets = array();
    foreach ($replies as $reply) {
      $tickets[$reply->getSupportTicketId()] = true;
    }
    
    $c->clear();
    $tickets = array_keys($tickets);
    $c->add(SupportTicketPeer::ID, $tickets, Criteria::IN);
    $c->add(SupportTicketPeer::STATUS, 'open');
    
    $tickets = SupportTicketPeer::doSelect($c, Propel::getConnection('support'));
    $this->tickets = $tickets;
    
    $dli = $support_userid.'_'.substr(md5($support_username.md5($support_password)), 0,10);
    $this->dli = $dli;
  }
}