<?php

class baseRepliesComponents extends sfComponents
{
  public function executeShortlist()
  {
    $errors = array();
    
    $request = sfContext::getInstance()->getRequest();
    
    if ($request->getMethod() == 'POST' && $request->hasParameter('formname') && $request->getParameter('formname') == 'reply') {
      
      print_r($_POST);
      
      if ($request->getParameter('reply') != '') {
        
        $reply = new Reply;
        $reply->setMessage($request->getParameter('reply'));
        $reply->setIp($_SERVER['REMOTE_ADDR']);
        $reply->setPdate(time());
        $reply->setObject(get_class($this->object));
        $reply->setObjectId($this->object->getId());
        
        if (zeusVisitor::getInstance()->isLoggedIn()) {
          // registred user
          $visitor = zeusVisitor::getInstance()->getVisitor();
          $reply->setVisitor($visitor->getName());
          $reply->setVisitorEmail($visitor->getEmail());
          $reply->setVisitorId($visitor->getId());
          $reply->save();
        }
      }
      else {
        $errors['reply'] = 'U heeft geen reactie ingevuld.'; 
      }
      
      if (!zeusVisitor::getInstance()->isLoggedIn()) {
        // unregistred user
        
        if ($request->getParameter('reply') == '') {
          $errors['reply'] = 'U heeft geen reactie ingevuld.'; 
        }
          
        if ($request->getParameter('reply-name') == '') {
          $errors['reply-name'] = 'Uw naam is een verplicht.';
        }
        
        if ($request->getParameter('reply-email') == '') {
          $errors['reply-email'] = 'Uw e-mail adres is een verplicht.';
        }
        else if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $request->getParameter('reply-email'))) {
          $errors['reply-email'] = 'U*w e-mail adres is niet geldig.';
        }
        
        if (!zeusCaptcha::getInstance('reply')->check()) {
          $errors['captcha'] = 'De beveiligingscode is niet juist.';
        }
            
        if (count($errors) == 0) {
          $reply->setVisitor($request->getParameter('reply-name'));
          $reply->setVisitorEmail($request->getParameter('reply-email'));
          $reply->save();
        }
        
        if (count($errors) == 0) {
          // message added, redirect to the same page to prevent doubleposts
          $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
          $action->redirect(sfContext::getInstance()->getUser()->getAttribute('visitor_return_url'));
        }
      }
   
    }
    
    $this->errors = $errors;
  }
}