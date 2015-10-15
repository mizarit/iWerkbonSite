<?php

class mailingadminActions extends zeusActions
{
  protected $model = 'Mailing';
  
  public function executeCopy()
  {
    $object = MailingPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($object);
    
    $new = new $this->model;
    $new->setTitle($object->getTitle().' kopie');
    $new->setMailinglist($object->getMailinglist());
    $new->setTemplate($object->getTemplate());
    $new->setContent($object->getContent());
    $new->save();
    
    $this->redirect('mailingadmin/edit?id='.$new->getId());
  }
  
  public function preSave($object)
  {
    $object->setStatus('draft');
    
    if ($this->getRequestParameter('mode') == 'send') {
      $object->setStatus('pending');
    }
    
    $object->setTemplate($this->getRequestParameter('template'));
    $object->setSite($this->getRequestParameter('site'));
    
    $mailinglists = array();
    foreach($_POST as $key => $value) {
      if (substr($key,0,12) == 'mailinglist_') {
        $mailinglists[] = substr($key, 12);
      }
    }
    $object->setMailinglist(implode('|', $mailinglists));
    $object->save();
    
    if ($this->getRequestParameter('sendmode') != '') {
      switch ($this->getRequestParameter('sendmode')) {
        case 'preview':
          $this->redirect('mailingadmin/preview?id='.$object->getId());
          break;
          
        case 'test':
          $this->redirect('mailingadmin/sendtestmailing?id='.$object->getId().'&email='.str_replace('.', '~~', $this->getRequestParameter('sendmodevalue')));
          break;
      }
    }
    
    if ($object->getStatus() == 'pending') {
      $this->redirect('mailingadmin/sendmailing?id='.$object->getId());
    }
  }
  
  public function executePreview()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    zeusRibbon::addButton(new zeusRibbonButton(array(
      'label' => 'Terug naar nieuwsbrief', 
      'icon'  => 'previous',
      'type'  => 'large', 
      'id'    => 'previous-btn',
      'callback' => "window.location.href='".url_for('mailingadmin/edit?id='.$this->getRequestParameter('id'))."';"
    )));
  
  }
  
  public function executeRender()
  {
    $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    $mailing = MailingPeer::retrieveByPk($this->getRequestParameter('id'), Propel::getConnection('mailing'));
    $template = $mailing->getTemplate();
    if (!$template) $template = 'Default';
    
    $cfg = zeusYaml::load(sfConfig::get('sf_root_dir').'/apps/frontend/config/app.yml');
    $sites = $cfg['all']['multisite']['cms'];
    if (isset($sites[$mailing->getSite()])) {
      $site = $sites[$mailing->getSite()];
    }
    else {
      $site = array_shift($sites);
    }
    
    $host = $site['url'];
      
    $preview = $action->getPartial('mailingadmin/mailing'.$template, array(
      'mailing' => $mailing,
      'user' => new User,
      'host' => $host
    ));
    
    $sites = $cfg['all']['multisite']['cms'];
    $site = array_shift($sites);
    $imagehost = $site['url'];
    
    $preview = str_replace('src="/img', 'src="'.$imagehost.'/img', $preview);
    $preview = str_replace('src="/uploads', 'src="'.$imagehost.'/uploads', $preview);
    $preview = str_replace('href="/', 'href="http://'.$_SERVER['HTTP_HOST'].'/', $preview);
    
    $this->preview = $preview;
    
    $this->setLayout(false);
  }
  
  public function executeSendtestmailing()
  {
    try
    {
      $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
      $request = sfContext::getInstance()->getRequest();
      $mailer = new Swift_Mailer(new Swift_MailTransport());
      
      $mailing = MailingPeer::retrieveByPk($this->getRequestParameter('id'));
      
      $template = $mailing->getTemplate();
      if (!$template) $template = 'Default';
      
      $cfg = zeusYaml::load(sfConfig::get('sf_root_dir').'/apps/frontend/config/app.yml');
      $sites = $cfg['all']['multisite']['cms'];
      if (isset($sites[$mailing->getSite()])) {
        $site = $sites[$mailing->getSite()];
      }
      else {
        $site = array_shift($sites);
      }
      
      $host = $site['url'];

      $html = $action->getPartial('mailingadmin/mailing'.$template, array(
        'mailing' => $mailing,
        'user' => new User,
        'host' => $host
      ));
      
      $imagehost = $_SERVER['HTTP_HOST'];
      
      $html = str_replace('src="/img', 'src="http://'.$imagehost.'/img', $html);
      $html = str_replace('src="/uploads', 'src="http://'.$imagehost.'/uploads', $html);
      $html = str_replace('href="/', 'href="http://'.$_SERVER['HTTP_HOST'].'/', $html);
      
      $to = str_replace('~~', '.', $this->getRequestParameter('email'));
     
      $message = new Swift_Message($mailing->getTitle(), $html, 'text/html');

      $message_from = zeusConfig::get('Nieuwsbrieven '.$site['title'], 'Afzender e-mail adres', 'input', 'info@'.$host);
      $message_name = zeusConfig::get('Nieuwsbrieven '.$site['title'], 'Afzender naam', 'input', $site['title']);
            
      $message->setFrom(array($message_from => $message_name));
        
      $message->setTo(array(
        $to
      ));
     
      $mailer->send($message);
    }
    catch (Exception $e)
    {
     // var_dump($e);
    }
    
    $this->redirect('mailingadmin/edit?id='.$this->getRequestParameter('id').'&send=true');
  }
  
  public function executeSendmailing()
  {
    $mailing = MailingPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($mailing);
    
    $mailinglists = explode('|', $mailing->getMailinglist());
    
    $c = new Criteria;
    $c->add(SubscriptionPeer::MAILINGLIST_ID, $mailinglists, Criteria::IN);
    
    $c->addGroupByColumn(SubscriptionPeer::MAILINGUSER_ID);
    $c->setDistinct();
    $subscriptions = SubscriptionPeer::doSelect($c);
    
    foreach ($subscriptions as $subscription) {
      $c->clear();
      $c->add(MailingSubscriptionPeer::MAILING_ID, $mailing->getId());
      $c->add(MailingSubscriptionPeer::MAILINGUSER_ID, $subscription->getMailinguserId());
      
      $mailing_subscription = MailingSubscriptionPeer::doSelectOne($c);
      
      if (!$mailing_subscription) {
        $mailing_subscription = new MailingSubscription; 
        $mailing_subscription->setMailinguserId($subscription->getMailinguserId());
        $mailing_subscription->setMailingId($mailing->getId());
        $mailing_subscription->setStatus('validate');
        $mailing_subscription->save();
      }
    }
    
    if ($this->hasRequestParameter('subscription')) {
      foreach($this->getRequestParameter('subscription') as $key => $value) {
        $mailing_subscription = MailingSubscriptionPeer::retrieveByPk($key);
        if ($mailing_subscription) {
          $mailing_subscription->setStatus($value);
          $mailing_subscription->save();
        }
      }
    }

    $c->clear();
    $c->add(MailingSubscriptionPeer::MAILING_ID, $mailing->getId());
    $mailing_subscriptions = MailingSubscriptionPeer::doSelect($c);
    $needs_validation = false;
    foreach ($mailing_subscriptions as $mailing_subscription) {
      if ($mailing_subscription->getStatus() == 'validate') {
        $needs_validation = true;
      }
    }
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    zeusRibbon::addButton(new zeusRibbonButton(array(
        'label' => 'Terug naar nieuwsbrief', 
        'icon'  => 'previous',
        'type'  => 'large', 
        'id'    => 'previous-btn',
        'callback' => "window.location.href='".url_for('mailingadmin/edit?id='.$this->getRequestParameter('id'))."';"
    )));
      
    if ($needs_validation) {
      zeusRibbon::addButton(new zeusRibbonButton(array(
        'label' => 'Verzenden', 
        'icon'  => 'mail_forward',
        'type'  => 'large', 
        'id'    => 'mailing-send-btn',
        'callback' => "sendMailing()"
      )), 'Verzenden');
    }
    else {
      zeusRibbon::addButton(new zeusRibbonButton(array(
        'label' => 'Terug naar lijst', 
        'icon'  => 'previous',
        'type'  => 'large', 
        'id'    => 'previous-btn',
        'callback' => "window.location.href='".url_for('mailingadmin/index')."';"
    )));
    }
    
    $this->getUser()->setAttribute('mailing', $this->getRequestParameter('id'));
    
    $this->needs_validation = $needs_validation;
    $this->mailing_subscriptions = $mailing_subscriptions;
  }
  
  public function executeUpdatestatus()
  {
    $c = new Criteria;
    $c->add(MailingSubscriptionPeer::MAILING_ID, $this->getUser()->getAttribute('mailing'));
    $mailing_subscriptions = MailingSubscriptionPeer::doSelect($c);
   
    $total = $send = $unsend = $issend = 0;
    $mailings = array();
    
    foreach ($mailing_subscriptions as $mailing_subscription) {
      if (!$mailing_subscription->getDate()) {
        if ($mailing_subscription->getStatus() != 'dontsend') {
          $total++;
        }
      }
      
      if ($mailing_subscription->getStatus() == 'pending') {
        $send++;
        $mailings[] = $mailing_subscription;
      }
      
      if ($mailing_subscription->getStatus() == 'send') {
        $total++;
        $issend++;
      }
    }
    
    $lockfile_path = sfConfig::get('sf_cache_dir'). '/mailinglock';
    $lockfile = @file_get_contents($lockfile_path);
    if ($lockfile) {
      if (time() - $lockfile > 60) {
        unlink($lockfile_path);
      }
    }

    if (!file_exists($lockfile_path)) {
    
      file_put_contents($lockfile_path, time());
      
      $starttime = time();
      foreach ($mailings as $mailing) {
        if ($mailing->getStatus() == 'pending') {
          
        
         // try
         // {
            $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
            $request = sfContext::getInstance()->getRequest();
            $mailer = new Swift_Mailer(new Swift_SmtpTransport('localhost'));
            $o_mailing = MailingPeer::retrieveByPk($this->getUser()->getAttribute('mailing'));
            $o_mailing->setStatus('sending');
            $o_mailing->save();
            
            $template = $o_mailing->getTemplate();
            if (!$template) $template = 'Default';
    
            $cfg = zeusYaml::load(sfConfig::get('sf_root_dir').'/apps/frontend/config/app.yml');
            $sites = $cfg['all']['multisite']['cms'];
            if (isset($sites[$o_mailing->getSite()])) {
              $site = $sites[$o_mailing->getSite()];
            }
            else {
              $site = array_shift($sites);
            }
            
            $host = isset($site['url']) ? $site['url'] : 'http://'.$_SERVER['HTTP_HOST'];
      
            $html = $action->getPartial('mailingadmin/mailing'.$template, array(
              'mailing' => $o_mailing,
              'user' => $mailing->getMailinguser(),
              'host' => $host
            ));
            
            $subject = $o_mailing->getTitle();
            $message_from = zeusConfig::get('Nieuwsbrieven '.$site['title'], 'Afzender e-mail adres', 'input', 'info@'.$host);
            $message_name = zeusConfig::get('Nieuwsbrieven '.$site['title'], 'Afzender naam', 'input', $site['title']);
            
            $html = str_replace('(/img/', '('.$_SERVER['HTTP_HOST'].'/img/', $html);
            $html = str_replace('src="/img', 'src="http://'.$_SERVER['HTTP_HOST'].'/img', $html);
            $html = str_replace('src="/uploads', 'src="http://'.$_SERVER['HTTP_HOST'].'/uploads', $html);
            $html = str_replace('href="/', 'href="http://'.$_SERVER['HTTP_HOST'].'/', $html);
            
            $message = new Swift_Message($subject, $html, 'text/html');
      
            $message->setFrom(array($message_from => $message_name));
              
            $message->setTo(array(
              $mailing->getMailinguser()->getEmail()
            ));
           
            $mailer->send($message);
         // }
         // catch (Exception $e)
         // {
         // }
      
          $mailing->setStatus('send');
          $mailing->setDate(time());
          $mailing->save();
        }
        
        $send--;
        $issend++;
        if (time() - $starttime > 1) {
          break;
        }
      }
      
      $ready = false;
      if ($send == 0) {
        $status = 'Klaar met verzenden van '.$total.' e-mail'.($total == 1?'':'s');
        $ready = true;
      }
      else {
        $status = 'Bezig met verzenden ( '.$issend. ' van '.$total.')';
      }
      
      if ($total == 0) {
        $ready = true;
        $percentage = 100;
      }
      else {
        $percentage = (100 / $total) * ($total - $send);
      }
      
      unlink($lockfile_path);
      
      if ($ready) {
        $mailing = MailingPeer::retrieveByPk($this->getUser()->getAttribute('mailing'));
        $mailing->setStatus('send');
        $mailing->setDate(time());
        $mailing->save();
      }
      echo json_encode(array('status' => $status, 'ready' => $ready, 'percentage' => $percentage));
    }
    
    exit;
    
  }
}