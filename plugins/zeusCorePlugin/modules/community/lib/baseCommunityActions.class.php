<?php

class baseCommunityActions extends sfActions
{
  public function executeLogin()
  {
    $errors = array();
    
    if ($this->getRequest()->getMethod() == 'POST' && $this->hasRequestParameter('formname') && $this->getRequestParameter('formname') == 'login') {
      
      
      $required = array(
        'username' => 'Uw gebruikersnaam', 
        'password' => 'Uw wachtwoord'
      );
      
      foreach ($required as $field => $error) {
        if (trim($this->getRequestParameter($field) == '')) {
          $errors[$field] = $error.' is verplicht';
        }
      }
      
      if (count($errors) == 0) {
        $c = new Criteria;
        $c->add(VisitorPeer::USERNAME, $this->getRequestParameter('username'));
        $c->add(VisitorPeer::PASSWORD, md5($this->getRequestParameter('password')));
        $c->add(VisitorPeer::STATUS, 0, Criteria::GREATER_THAN);
        $visitor = VisitorPeer::doSelectOne($c);
        
        if (!$visitor) {
          $errors['global'] = 'De gebruikersnaam of wachtwoord is niet juist';
        }
        else {
          zeusVisitor::getInstance()->doLogin($visitor);
        }
      }
    }
    
    $this->errors = $errors;
  }
  
  public function executeLogoff()
  {
    zeusVisitor::getInstance()->doLogoff();
  }
  
  public function executeRegister()
  {
    $errors = array();
    
    if ($this->getRequest()->getMethod() == 'POST' && $this->hasRequestParameter('formname') && $this->getRequestParameter('formname') == 'register') {
      
      
      $required = array(
        'name' => 'Uw naam', 
        'username' => 'Uw gebruikersnaam', 
        'password' => 'Uw wachtwoord',
        'password-2' => 'Uw controle wachtwoord',
        'email' => 'Uw e-mail adres'
      );
      
      foreach ($required as $field => $error) {
        if (trim($this->getRequestParameter($field) == '')) {
          $errors[$field] = $error.' is verplicht.';
        }
      }
      
      if (!$this->hasRequestParameter('accept')) {
        $errors['accept'] = 'U heeft de algemene voorwaarden en huisregels niet geaccepteerd.';
      }
        
      if (!isset($errors['password'])) {
        if ($this->getRequestParameter('password') != $this->getRequestParameter('password-2')) {
          $errors['password'] = 'De ingevoerde wachtwoorden zijn niet gelijk.';
        }
      }
      
      if (count($errors) == 0) {
        
        $c = new Criteria;
        $c->add(VisitorPeer::USERNAME, $this->getRequestParameter('username'));
        $visitor = VisitorPeer::doSelectOne($c);
        if ($visitor) {
          $errors['username'] = 'De gewenste gebruikersnaam is al in gebruik.';
        } 
        
        if (count($errors) == 0) {
          $visitor = new Visitor;
          $visitor->setName($this->getRequestParameter('name'));
          $visitor->setUsername($this->getRequestParameter('username'));
          $visitor->setPassword(md5($this->getRequestParameter('password')));
          $visitor->setEmail($this->getRequestParameter('email'));
          $visitor->setStatus(0);
          $visitor->setLastip($_SERVER['REMOTE_ADDR']);
          $visitor->save();
          
          try
          {
            $mailer = new Swift_Mailer(new Swift_SmtpTransport('localhost'));
            $message = new Swift_Message(
              'Bevestig uw registratie', 
              $this->getPartial('community/confirmation', array(
                'name' => $this->getRequestParameter('name'),
                'username' => $this->getRequestParameter('username'),
                'password' => $this->getRequestParameter('password'),
                'code'      => substr(md5($this->getRequestParameter('username').$this->getRequestParameter('email').'salt'), 0, 8),
                'object' => $visitor
              ))
            );
            
            $message->setFrom(array('info@star-people.nl' => 'StarPeople'));
            $message->setTo(array($this->getRequestParameter('email') => $this->getRequestParameter('name')));
           
            $mailer->send($message);
            
            $this->redirect('community/pleaseconfirm');
          }
          catch (Exception $e)
          {
          }
          
        
        }
       
      }
    }
    
    
    $this->errors = $errors;
    
  }
  
  public function executePleaseconfirm()
  {
    
  }
  
  public function executeActivate()
  {
    $visitor = VisitorPeer::retrieveByPk($this->getRequestParameter('u'));
    $this->forward404Unless($visitor); 
    
    $code = substr(md5($visitor->getUsername().$visitor->getEmail().'salt'), 0, 8);
    if ($code == $this->getRequestParameter('code')) {
      $visitor->setStatus(1);
      $visitor->save();
      zeusVisitor::getInstance()->doLogin($visitor);
    }
    
    $this->redirect('@homepage');
    
  }
  
}