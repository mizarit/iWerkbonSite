<?php

class mailingActions extends baseMailingActions 
{
  public function executeIndex()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_index', 'nieuwsbrief');
    
    $errors = array();
    $message = '';
    
    if ($this->hasRequestParameter('accept-tagletter')) {
      
      
      if ($this->getRequestParameter('email') == '' ) {
        $errors['email'] = 'Je hebt geen e-mail adres ingevoerd.';
      }
      else if (is_string($this->validateField('email', $this->getRequestParameter('email')))) {
        $errors['email'] = 'Je hebt geen geldig e-mail adres ingevoerd.';
      }
      
      if ($this->getRequestParameter('zipcode') == '' ) {
        $errors['zipcode'] = 'Je hebt geen postcode ingevoerd. We gebruiken je postcode om zo relevant mogelijke aanbiedingen in jouw regio te vinden.';
      }
      else if (is_string($this->validateField('zipcode', $this->getRequestParameter('zipcode'), array('country' => 'NL', 'label' => 'postcode')))) {
        $errors['zipcode'] = 'Je hebt geen geldige postcode ingevoerd.';
      }
      
      if (count($this->getRequestParameter('category')) == 0) {
        $errors['category'] = 'Kies ten minste een categorie waar je van op de hoogte gehouden wilt worden.';
      }
      
      if (count($errors) == 0) {
        $consumer = new Consumer;
        $consumer->setEmail($this->getRequestParameter('email'));
        $consumer->setZipcode($this->getRequestParameter('zipcode'));
        
        $contact = ContactPeer::retrieveFor($consumer);
        $contact->setTargetmail($this->getRequestParameter('category'));
        $contact->setFrequency($this->getRequestParameter('newsletter-frequency'));
        $contact->save();
      
        $message = 'Je hebt je aangemeld om op de hoogte gehouden te worden van aanbiedingen die voor jou relevant zijn.';
      }
    }
    
    $this->message = $message;
    $this->errors = $errors;
  }
  
  public function executeUnsubscribe()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_index', 'nieuwsbrief');
    pvCrumblepath::add('@mailing_unsubscribe', 'uitschrijven');
    parent::executeUnsubscribe();
  }
  
  public function executeSubscribe()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_index', 'nieuwsbrief');
    pvCrumblepath::add('@mailing_subscribe', 'inschrijven');
    parent::executeSubscribe();
  }
  
  public function executeUserexists()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_index', 'nieuwsbrief');
    parent::executeUserexists();
  }
  
  public function executeConfirm()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_confirm', 'bevestigen');
    parent::executeConfirm();
  }
  
  
  public function executeViewonline()
  {
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add('@mailing_index', 'nieuwsbrief');
    parent::executeViewonline();
  }
  
  
  public function executeRender()
  {
    $this->getUser()->setAttribute('no-mobile', true);
    
    $object = ContactmailingPeer::retrieveByPk($this->getRequestParameter('id'));
    $o = 'mailing'.$object->getTemplate();
    $plugin = new $o($object);
    $cfg = $object->getTemplatecfg();
    if ($cfg != '') {
      $cfg = unserialize($cfg);
      foreach ($cfg['blocks'] as $block => $value) {
        $plugin->setBlock($block, $value);
      }
      foreach ($cfg['texts'] as $block => $value) {
        $plugin->setText($block, $value);
      }
    }
    
    $this->plugin = $plugin;
   /* 
    for ($x = 0; $x < $plugin->blocks; $x++) {
      $v = $this->getRequestParameter('value-'.$x);
      $plugin->setBlock($x, $v);
      if ($v == 'text') {
        $plugin->setText($x, $this->getRequestParameter('text-'.$x));
      }
    }
    */
    
    echo $plugin->render();
    exit;
  }

  public function executePixel()
  {
    $i = $this->getRequestParameter('i');
    $contactmailing_contant = ContactmailingContactPeer::retrieveByPk($i);
    if ($contactmailing_contant) {
      if (!$contactmailing_contant->getOpened()) {
        $contactmailing_contant->setOpened(true);
        $contactmailing = $contactmailing_contant->getContactmailing();
        $contactmailing->setOpened($contactmailing->getOpened() + 1);
        $contactmailing->save();
        $contactmailing_contant->save();
      }
    }
    
    $img = imagecreatetruecolor(1,1);
    $color = imagecolorallocatealpha($img,255,255,255,255);
    imagefill($img, 0, 0, $color);
    header('Content-type: image/png');
    imagepng($img);
    imagedestroy($img);
    
    exit;
  }
  
  protected function validateField($validator, $value = '', $cfg = array())
  {
    switch ($validator) {
      case 'numeric':
        if ($value == '') return true;
        if (!is_numeric($value)) {
          return $cfg['label'].' is geen numerieke waarde.';
        }
        break;
        
      case 'min':
        if (is_array($cfg['min'])) {
          switch ($cfg['min']['type']) {
            case 'checkbox':
              if (count($this->getRequestParameter($cfg['min']['field'])) < $cfg['min']['min']) {
                if ($cfg['min']['min'] == 1) {
                  return 'Van '.strtolower($cfg['label']).' moet er minimaal '.$cfg['min']['min'].' geselecteerd zijn.';
                }
                else {
                  return 'Van '.strtolower($cfg['label']).' moeten er minimaal '.$cfg['min']['min'].' geselecteerd zijn.';
                }
              }
          }
        }
        elseif ($value < $cfg['min']) {
          return $cfg['label'].' moet minimaal '.$cfg['min'].' zijn.';
        }
        break;
        
      case 'maxlength':
        if (strlen($value) > $cfg['maxlength']) {
          return $cfg['label'].' mag maximaal '.$cfg['maxlength'].' tekens lang zijn.';
        }
        break;
        
      case 'minlength':
        if (strlen($value) < $cfg['minlength']) {
          return $cfg['label'].' moet minstens '.$cfg['minlength'].' tekens lang zijn.';
        }
        break;
        
      case 'email':
        if ($value == '') return true;
        
        $validator = new sfValidatorEmail();
        
        try {
          $validator->clean($value);
        }
        catch (sfValidatorError $e)
        {
          return $cfg['label'].' is geen geldig e-mail adres.';
        }
        break;
        
      case 'password':
        $password = $this->getRequestParameter('password1');
        if ($password != '') {
          if (strlen($password) < 5) {
            return $cfg['label'].' moet minimaal 5 tekens lang zijn.';
          }
          elseif($password != $this->getRequestParameter('password2')) {
            return $cfg['label'].' is niet hetzelfde als het controle-wachtwoord.';
          }
        }
        break;
        
      case 'username':
        if ($value == '') return true;
        // also a username to validate
        $user = UserPeer::retrieveByPk($this->getUser()->getAttribute('user_id'));
        if (!$user) {
          $user = new User;
        }
        
        $c = new Criteria;
        $c->add(UserPeer::USERNAME, $value);
        $users = UserPeer::doSelect($c);
        foreach ($users as $testUser) {
          if ($testUser->getId() != $user->getId()) {
            return 'Deze gebruikersnaam is al in gebruik.';
          }
        }
        break;
        
      case 'url':
        if ($value == '') return true;
        
        $validator = new sfValidatorUrl();
        
        try {
          $validator->clean($value);
        }
        catch (sfValidatorError $e)
        {
          return $cfg['label'].' is geen geldige URL.';
        }
        break;
        
      case 'color':
        if ($value == '') return true;
        break;
        
      case 'phone':
        if ($value == '') return true;
        switch ($cfg['country']) {
          case 'NL':
            $test = preg_match('#^0[1-9][0-9]{0,2}-?[1-9][0-9]{5,7}$#', $value) && (strlen( str_replace(array('-', ' '), '', $value )) == 10);
            if (!$test) {
              return $cfg['label'].' is geen geldig telefoonnummer. ( gebruik geen spaties, alleen cijfers en eventueel een streepje )';
            }
            break;
            
        }
        break;
        
      case 'mobile':
        if ($value == '') return true;
        switch ($cfg['country']) {
          case 'NL':
            $test = preg_match('#^06-?[1-9][0-9]{7}$#', $value) && (strlen( str_replace(array('-', ' '), '', $value )) == 10);
            if (!$test) {
              return $cfg['label'].' is geen geldig mobiel telefoonnummer. ( gebruik geen spaties, alleen cijfers en eventueel een streepje )';
            }
            break;
            
        }
        break;
        
      case 'zipcode':
        if ($value == '') return true;
        switch ($cfg['country']) {
          case 'NL':
            if (!preg_match('#^[1-9][0-9]{3}\h*[A-Z]{2}$#i', $value)) {
              return $cfg['label'].' is geen geldige postcode. ( 1234 AA )';
            }
            break;
        }
        break;
    }
    
    return true;
  } 
}