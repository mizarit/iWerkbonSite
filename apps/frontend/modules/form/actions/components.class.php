<?php

class formComponents extends sfComponents
{
  public function executeShow()
  {
    $form_config = zeusYaml::load('form-'.$this->name.'.yml', 'form');
    $this->form = new formForm(array(), array('form-config' => $form_config));
  }
} 