<?php

class zeusRibbonButton 
{
  protected $config = array(
    'label'     => 'Label',
    'type'      => 'large',
    'icon'      => 'default-large',
    'callback'  => 'zeusRibbon.post'
  );
  
  public function __construct($config = array())
  {
    foreach ($config as $key => $value) 
    {
      $this->config[$key] = $value;
    }
  }
  
  public function getConfig()
  {
    return $this->config;
  }
  
  public function get()
  {
    $action = '';
    
    if ($this->config['callback']) {
      $action = ' onclick="'.$this->config['callback'].'"';
    }
    
    $id = isset($this->config['id']) ? ' id="'.$this->config['id'].'"' : '';
    
    $disabled = isset($this->config['disabled']) && $this->config['disabled'] ? ' zeus-button-disabled' : '';
    
    switch ($this->config['type']) {
      case 'large':
        return '<div'.$id.$action.' class="zeus-button-1'.$disabled.'"><div><img src="/zeusCore/img/crystal/32x32/actions/'.$this->config['icon'].'.png" alt="'.$this->config['label'].'"> <p class="zeus-button-label">'.$this->config['label'].'</p></div></div>';
        break;
        
      case 'small':
        return '<div'.$id.$action.' class="zeus-button-2'.$disabled.'"><div><img src="/zeusCore/img/crystal/16x16/actions/'.$this->config['icon'].'.png" alt="'.$this->config['label'].'"> <p class="zeus-button-label">'.$this->config['label'].'</p></div></div>';
        break;
        
      case 'large-pulldown':
        
        if ($this->config['pulldown']['callback']) {
          $options = isset($this->config['pulldown']['parameters']) ? ", '{$this->config['pulldown']['parameters']}'" : '';
          $default = $this->config['pulldown']['default'];
          $action = ' onclick="'.$this->config['pulldown']['callback']."('{$default}'{$options});\"";
        }
    
        $pulldown_title = $this->config['pulldown']['title'];
        $pulldown_options = $this->config['pulldown']['options'];
        $ret = '<div class="zeus-button-1'.$disabled.'"><div><img'.$action.' src="/zeusCore/img/crystal/32x32/actions/'.$this->config['icon'].'.png" alt="'.$this->config['label'].'"> <p class="zeus-button-label zeus-arrow-down-newline">'.$this->config['label'].'<span onclick="zeusRibbon.startBox(\''.$this->config['id'].'\');"><img class="trigger" src="/zeusCore/img/ribbon/arrow-down.gif" alt=""></span></p></div></div>';
        $ret .= <<<EOT
      <div class="zeus-modal" id="{$this->config['id']}-container">
        <div class="zeus-modal-box-1">
          <div class="zeus-modal-content" id="{$this->config['id']}-inner-container">
            <p class="zeus-modal-p-1">{$pulldown_title}</p>
            <div class="zeus-modal-div-1">
              <ul class="element-list">\n
EOT;
        foreach ($pulldown_options as $var => $label) {
          $options = isset($this->config['pulldown']['parameters']) ? ", '{$this->config['pulldown']['parameters']}'" : '';
          $ret .= <<<EOT
          	    <li class="form-element" onclick="{$this->config['pulldown']['callback']}('{$var}'{$options});">
          	      <div class="list-item-container"><div class="list-item-inner-container">{$label}</div></div>
          	      <div class="list-item-footer-container"><div class="list-item-footer-left-container"></div></div>
          	    </li>\n
EOT;
        }
        
        $ret .= <<<EOT
              </ul>
            </div>
          </div>
        </div>
      <div class="zeus-modal-box-2"><div></div></div>
    </div>\n
EOT;
        return $ret;
        break;
        
      case 'html':
        return "<div{$id}{$action} class=\"zeus-button-2 zeus-button-flat\"><div>{$this->config['content']}</div></div>";
        break;
    }
  }
}