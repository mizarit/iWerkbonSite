<?php

class zeusRibbonButtonBack extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Terug naar lijst',
    'type'      => 'large',
    'icon'      => 'previous'
  );
  
  public function get()
  {
    $path = url_for($this->config['path']);
    $this->config['callback'] = "window.location.href='{$path}'";
    return parent::get();
  }
}