<?php

class zeusRibbonButtonEdit extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Bewerken',
    'type'      => 'large',
    'icon'      => 'xedit',
    'callback'  => ""
  );
  
  public function get()
  {
    $path = url_for($this->config['path']);
    if (isset($this->config['object'])) {
      $id = $this->config['object']->getId();
      $this->config['callback'] .= "$('list-loader').style.display='block';window.location.href='{$path}/id/{$id}';";
    }
    else {
      $this->config['callback'] = "$('list-loader').style.display='block';window.location.href='{$path}/id/'+zeusSelectedRow;";
    }
    
    return parent::get();
  }
}