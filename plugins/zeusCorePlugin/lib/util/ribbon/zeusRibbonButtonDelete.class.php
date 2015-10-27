<?php

class zeusRibbonButtonDelete extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Verwijderen',
    'type'      => 'large',
    'icon'      => 'fileclose',
    'callback'  => "if(confirm('Weet je zeker dat je dit item wilt verwijderen?'))"
  );
  
  public function get()
  {
    $path = url_for($this->config['path']);
    if (isset($this->config['object'])) {
      $id = $this->config['object']->getId();
      $this->config['callback'] .= "if(confirm('Weet je zeker dat je dit item wilt verwijderen?')){ $('list-loader').style.display='block';window.location.href='{$path}/id/{$id}';}";
    }
    else {
      $this->config['callback'] = "if(confirm('Weet je zeker dat je dit item wilt verwijderen?')){ $('list-loader').style.display='block';window.location.href='{$path}/id/'+zeusSelectedRow;return false;}";
    }
    
    return parent::get();
  }
}