<?php

class formActions extends baseFormActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $form_config = zeusYaml::load('form-'.$this->getRequestParameter('f').'.yml');
    
    $this->forward404Unless($form_config);
    
    $this->form_config = $form_config;
    
    if(isset($form_config['layout'])) {
      $this->setLayout($form_config['layout']);
    }
    
    if (!$request->isMethod('post')) {
      if($this->hasRequestParameter('product')) {
        $product = ShopProductPeer::retrieveByPk($this->getRequestParameter('product'));
        if ($product) {
          $product->setCulture($this->getUser()->getCulture());
          $form_config['fields']['opmerking']['value'] = 'Ik wil graag meer informatie over "'.$product->getTitle().'"';
        }
      }
    }
    
    $this->form = new formForm(array(), array('form-config' => $form_config));
   
    $this->page = PagePeer::retrieveByPk($form_config['page']);
     
    if ($this->page) {
      sfContext::getInstance()->getResponse()->setTitle($this->page->getTitle());
    }
    else {
      sfContext::getInstance()->getResponse()->setTitle($form_config['title']);
    }
    
    if ($request->isMethod('post') && !$this->hasRequestParameter('directions-form'))
    {
      $this->form->bind($request->getParameter('form_form'), $request->getFiles('form_form'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        $formvalues = array();
        
      	foreach ($form_config['fields'] as $field => $cfg) {
      	  switch ($cfg['type']) {
      	    case 'input':
      	    case 'textarea':
      	    default:
      	      $value = $values[$field];
      	      break;
      	      
      	    case 'file':
      	      $file = $this->form->getValue($field);
 
              $filename = 'uploaded_'.sha1($file->getOriginalName());
              $extension = $file->getExtension($file->getOriginalExtension());
              $file->save(sfConfig::get('sf_upload_dir').'/'.$filename.$extension);

              $value = '/'.$filename.$extension;
      	      break;
      	  }
      	  
      	  $formvalues[$field] = $value;
      	  
      	}
      	
      	$formdata = new Formdata;
      	$formdata->setTitle($this->getRequestParameter('f'));
      	$formdata->setDate(time());
      	$formdata->setName($values[$form_config['namefield']]);
      	$formdata->setData(serialize($formvalues));
      	$formdata->save();
      	
      	$this->redirect('form/thankyou?f='.$this->getRequestParameter('f'));
      }
    }
  }
  
  public function executeThankyou()
  {
    $this->setLayout('layout-home');
    
    $c = new Criteria;
    $c->addDescendingOrderByColumn(FormdataPeer::ID);
    $this->formdata = FormdataPeer::doSelectOne($c);
    
    try
    {
      $action = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
      $request = sfContext::getInstance()->getRequest();
      $mailer = new Swift_Mailer(new Swift_SmtpTransport('localhost'));
      $message = new Swift_Message(
        'Formulier ingevuld: '.$this->formdata->getTitle(), 
        $action->getPartial('form/email', array(
          'object' => $this->formdata
        ))
      );
      
      $email = zeusConfig::get('Formulieren', "E-mail adres formulier '".$this->formdata->getTitle()."'", 'input', 'info@'.$_SERVER['HTTP_HOST']);

      $message->setFrom(array('info@'.$_SERVER['HTTP_HOST'] => $_SERVER['HTTP_HOST']));
      $message->setTo(array($email));
     
      $mailer->send($message);
    }
    catch (Exception $e)
    {
    }
  }
}
