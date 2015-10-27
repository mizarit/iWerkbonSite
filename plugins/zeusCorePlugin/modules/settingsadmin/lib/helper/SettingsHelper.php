<?php

function list_settings_save($config = array())
{
  return new zeusRibbonButtonSave(array('form' => 'form-1'));
}

function list_settings_form($config)
{
  use_helper('ZeusEdit');
  use_helper('Form');
  $c = new Criteria;
  $c->addAscendingOrderByColumn(GlobalSettingPeer::GGROUP);
  $c->addAscendingOrderByColumn(GlobalSettingPeer::TITLE);
  
  if (sfContext::getInstance()->getRequest()->hasParameter('message')) {
    echo '<ul class="form-messages"><li>'.sfContext::getInstance()->getRequest()->getParameter('message').'</li></ul>';
  }
  echo '<form method="post" action="#" id="form-1"><fieldset><legend></legend>';
  $block = '';
  $settings = GlobalSettingPeer::doSelect($c);
  foreach ($settings as $setting) {
    if ($block != $setting->getGgroup()) {
      echo '<h3>'.$setting->getGgroup().'</h3>';
      $block = $setting->getGgroup();
    }
    
    switch ($setting->getGtype()) {
      case 'input':
        echo form_row('setting-'.$setting->getId(), input_tag('setting-'.$setting->getId(), $setting->getValue()), array('label' => $setting->getTitle()));
        break;
    }
  }
  
  echo '</fieldset></form>';
}