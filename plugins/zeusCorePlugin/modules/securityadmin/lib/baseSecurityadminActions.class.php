<?php

class baseSecurityadminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    
  }
  
  public function executeLogin(sfWebRequest $request)
  {
    $this->form = new securityadminLoginForm;
    
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('securityadmin'));
      if ($this->form->isValid())
      {
      	$this->getUser()->setAuthenticated(true);
        $this->redirect('@homepage');
      }
    }
  }
  
  public function executeLogoff(sfWebRequest $request)
  {
  	$this->getUser()->setAuthenticated(false);
    $this->redirect('@homepage');
  }
}