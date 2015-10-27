<?php

class zeusRibbonButtonSave extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Opslaan',
    'type'      => 'large',
    'icon'      => 'filesave',
    'callback'  => 'zeusRibbon.save'
  );
  
  public function __construct($config = array())
  {
    foreach ($config as $key => $value) {
      $this->config[$key] = $value;
    }
  }
  
  public function get()
  {
    $form = $this->config['form'];

    $this->config['callback'] = "$('{$form}').submit()";
    return parent::get();
  }
}