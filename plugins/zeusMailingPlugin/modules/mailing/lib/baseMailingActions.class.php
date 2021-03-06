<?php

class baseMailingActions extends sfActions
{
  private function getConnection()
  {
    return Propel::getConnection('mailing');
  }
  
  public function executeViewonline()
  {
    
  }
  
  public function executeUnsubscribe()
  {
    $mailinguser = MailinguserPeer::retrieveByPk($this->getRequestParameter('id'), Propel::getConnection('mailing'));
    if (!$mailinguser) {
      $this->redirect('@homepage');
    }
    
    $test = substr(md5($mailinguser->getEmail()), 0, 6);
    
    if (!$test == $this->getRequestParameter('hash')) {
      $this->redirect('@homepage');
    }
    
    //mail('info@onlineafspraken.nl','Uitschrijving nieuwsbrief', $mailinguser->getEmail());
    
    
    $c = new Criteria;
    $c->add(SubscriptionPeer::MAILINGUSER_ID, $mailinguser->getId());
    SubscriptionPeer::doDelete($c, Propel::getConnection('mailing'));
    
    $c->clear();
    $c->add(MailinguserPeer::ID, $mailinguser->getId());
    MailinguserPeer::doDelete($c, Propel::getConnection('mailing'));
    
  }
  
  public function executeSubscribe()
  {
    if (!$this->hasRequestParameter('mailinglist')) {
      $this->redirect('@homepage');
    }
    
    if ($this->getRequestParameter('email') == 'je@e-mail.adres') {
      $this->redirect('@homepage');
    }
    
    
    $consumer = new Consumer;
    $consumer->setEmail($this->getRequestParameter('email'));
    
    $contact = ContactPeer::retrieveFor($consumer);
    if ($contact->getHasmailing()) {
      if ($this->getRequestParameter('modus') == 'subscribe') {
        $this->forward('mailing', 'userexists');
      }
    }
    
    try
    {
      $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
      $request = sfContext::getInstance()->getRequest();
      $mailer = new Swift_Mailer(new Swift_MailTransport());
      
      $cfg = zeusYaml::load(sfConfig::get('sf_root_dir').'/apps/frontend/config/app.yml');
      
      $sites = $cfg['all']['multisite']['cms'];
      $msite = false;
      foreach ($sites as $site)
      {
        if(isset($site['active'])) {
          $msite = $site;
        }
      }
      
      if (!$msite) {
        $msite = array_shift($sites);
      }
      
      $template = $this->getRequestParameter('modus') == 'subscribe' ? 'mailSubscribe' : 'mailUnsubscribe';
      $html = $action->getPartial('mailing/'.$template, array(
        'mailinglist_id' => $this->getRequestParameter('mailinglist'),
        'email' => $this->getRequestParameter('email')
      ));
      
      $subject = $this->getRequestParameter('modus') == 'subscribe' ? 'Aanmelden' : 'Afmelden';
      $message = new Swift_Message($subject.' nieuwsbrief', $html, 'text/html');
      
      $message_from = zeusConfig::get('Nieuwsbrieven '.$msite['title'], 'Afzender e-mail adres', 'input', 'info@'.str_replace('http://', '', $msite['url']));
      $message_name = zeusConfig::get('Nieuwsbrieven '.$msite['title'], 'Afzender naam', 'input', $msite['title']);
            
      $message->setFrom(array($message_from => $message_name));
        
      $message->setTo(array(
        $this->getRequestParameter('email')
      ));
     
      $mailer->send($message);
    }
    catch (Exception $e)
    {
    }
    
    $this->modus = $this->getRequestParameter('modus');
  }
  
  public function executeUserexists()
  {
    
  }
  
  public function executeConfirm()
  {
    $email = base64_decode(str_replace('__', '=', $this->getRequestParameter('e')));
    $mailinglist_id = $this->getRequestParameter('l');
    $hash = $this->getRequestParameter('h');
    $modus = $this->getRequestParameter('m');
    
    $test = substr(md5($email.$mailinglist_id),6,6);
    
    if ($test != $hash) 
    {
      $this->redirect('@homepage');
    }
    
    $consumer = new Consumer;
    $consumer->setEmail($email);
    
    $contact = ContactPeer::retrieveFor($consumer);
    

    if ($modus == 'subscribe') 
    {
      $contact->setNewsletter(true);
    }
    else {
      $contact->setNewsletter(false);
    }
    
    $contact->save();
    
    $this->modus = $modus;
  }
  
  
}