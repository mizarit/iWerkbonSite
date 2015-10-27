<?php

class securityadminLoginForm extends sfForm
{
  public function configure()
  {
    $remember = sfContext::getInstance()->getRequest()->getCookie('username') ? true : false;
    $this->setWidgets(array(
      'username'   => new sfWidgetFormInput(array(), array('value' => sfContext::getInstance()->getRequest()->getCookie('username'))),
      'password'   => new sfWidgetFormInputPassword(array(), array('value' => sfContext::getInstance()->getRequest()->getCookie('password'))),
      'remember'   => new sfWidgetFormInputCheckbox(array(), $remember ? array('class' => 'checkbox', 'checked' => 'checked') : array('class' => 'checkbox') ),
    ));
    
    $this->widgetSchema->setLabel('username', 'Gebruikersnaam');
    $this->widgetSchema->setLabel('password', 'Wachtwoord');
    $this->widgetSchema->setLabel('remember', 'Onthoud mijn inloggegevens');
    
    $this->widgetSchema->setFormFormatterName('zeus');
    
    $this->widgetSchema->setNameFormat('securityadmin[%s]');
    
    
    $this->setValidators(array(
      'username' => new sfValidatorCallback(
	       array(
	         'callback' => array ('zeusLoginValidator', 'execute'),
	         'arguments' => array('username', 'password', $this->widgetSchema->getNameFormat()),
	         'required' => true 
	       ),
	       array(
	         'invalid' => 'De gebruikersnaam of wachtwoord is onjuist',
	         'required' => 'Gebruikersnaam is een verplicht veld'
	       )
	     ),
      'password' => new sfValidatorString(
	       array(
	         'required' => true, 
	         'min_length' => 3
	       ),
	       array(
	         'required' => 'Wachtwoord is een verplicht veld',
	         'min_length' => 'Het wachtwoord is minimaal 3 tekens lang'
	       )
	     ), 
	     'remember' => new sfValidatorPass()
    ));
  }
  
}