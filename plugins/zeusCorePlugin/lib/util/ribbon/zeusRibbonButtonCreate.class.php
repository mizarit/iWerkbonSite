<?php

class zeusRibbonButtonCreate extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Nieuw',
    'type'      => 'large',
    'icon'      => 'new_window'
  );
  
  public function get()
  {
    $this->config['callback'] = "window.location.href='{$this->config['path']}'";
    return parent::get();
  }
}