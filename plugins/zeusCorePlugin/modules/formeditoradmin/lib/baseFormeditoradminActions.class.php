<?php

class baseFormeditoradminActions extends zeusActions 
{
  protected $model = 'Form';  
  
  public function executeIndex(sfWebRequest $request)
  {
     sfContext::getInstance()->getUser()->setAttribute('formrows', null);
     
    parent::executeIndex($request);
  }
  public function postSave($object)
  {
    $tabindex = 0;
    $c = new Criteria;
    $c->add(FormrowPeer::FORM_ID, $object->getId());
    FormrowPeer::doDelete($c);
    
    foreach (sfContext::getInstance()->getUser()->getAttribute('formrows') as $row) {
      $tabindex++;
      $formrow = new Formrow;
      $formrow->setTitle($row['label']);
      $formrow->setFormId($object->getId());
      $formrow->setRtype($row['type']);
      $formrow->setRvalidator($row['rvalidator']);
      $formrow->setRvalidatorvalue($row['rvalidatorvalue']);
      $formrow->setRvalue($row['rvalue']);
      $formrow->setRoptions($row['roptions']);
      $formrow->setRrequired($row['rrequired']);
      $formrow->setTabindex($tabindex);
      $formrow->save();
    }
  }
  
  public function executeForm()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Formeditoradmin', 'formeditoradmin');
    
    $rtypes = edit_formeditoradmin_rtypes();
    
    $rows = sfContext::getInstance()->getUser()->getAttribute('formrows');
    
    if ($this->hasRequestParameter('rtype')) {
      $rows[] = array(
        'type' => $this->getRequestParameter('rtype'),
        'label' => 'Nieuwe regel',
        'rvalidator' => '',
        'rvalidatorvalue' => '',
        'rvalue' => '',
        'roptions' => '',
        'rrequired' => false
      );
      sfContext::getInstance()->getUser()->setAttribute('formrows', $rows);
    }
    
    if ($this->hasRequestParameter('deleterow')) {
      unset($rows[$this->getRequestParameter('deleterow')]);
      sfContext::getInstance()->getUser()->setAttribute('formrows', $rows);
    }
    
    if ($this->hasRequestParameter('editrow')) {
      $rows[$this->getRequestParameter('editrow')]['rvalidator'] = $this->getRequestParameter('rvalidator');
      $rows[$this->getRequestParameter('editrow')]['roptions'] = $this->getRequestParameter('roptions');
      $rows[$this->getRequestParameter('editrow')]['rrequired'] = $this->getRequestParameter('rrequired');
      $rows[$this->getRequestParameter('editrow')]['rvalidatorvalue'] = $this->getRequestParameter('rvalidatorvalue');
      $rows[$this->getRequestParameter('editrow')]['rvalue'] = $this->getRequestParameter('rvalue');
      
      sfContext::getInstance()->getUser()->setAttribute('formrows', $rows);
    }

    if ($this->hasRequestParameter('editorId')) {
      // label editor
      $value = $this->getRequestParameter('value');
      list($a,$b,$id) = explode('-', $this->getRequestParameter('editorId'));
      $rows[$id]['label'] = $value;
      sfContext::getInstance()->getUser()->setAttribute('formrows', $rows);
      echo $value;
      exit;
    }
    
    $this->rows = $rows;
  }
  
  public function executeBrowser()
  {
    $c = new Criteria;
    $c->addAscendingOrderByColumn(FormPeer::TITLE);
    $this->forms = FormPeer::doSelect($c);
    
    $this->setLayout('popup');
  }
  
  public function executePreview()
  {
    $form = FormPeer::retrieveByPk($this->getRequestParameter('id'));
    header('Content-type: image/png');
    $image = imagecreatetruecolor(400,50);
    $color1 = imagecolorallocate($image, 206,206,206);
    $color2 = imagecolorallocate($image, 255,255,255);
    $color3 = imagecolorallocate($image, 48,48,48);
    imagefill($image,0,0,$color2);
    imagerectangle($image,0,0,399,49,$color1);
    $fontfile = sfConfig::get('sf_root_dir').'/plugins/zeusCorePlugin/lib/vendor/fonts/verdanab.ttf';
    imagettftext($image,9,0,20,30,$color3, $fontfile, ucfirst($form->getTitle()).' formulier');
    imagepng($image);
    exit;
  }
}