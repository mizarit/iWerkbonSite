<?php

class zeusRibbonButtonI18N extends zeusRibbonButton
{
  protected $config = array(
  );
  
  public function get()
  {

    $pulldown = select_tag('culture', options_for_select(array_combine($this->config['cultures'],$this->config['cultures']), $this->config['culture']), array('style' => 'width:110px;', 'onchange' => 'i18n.SwitchCulture(this.value);'));
    
    $ret = '<div class="zeus-button-2"><div><p class="zeus-button-label">Huidige taal '.$pulldown.'</p></div></div>';
    $ret .= '<div class="zeus-button-2 zeus-button-disabled" id="delete-i18n-btn" onclick="i18n.deleteCulture();"><div><img src="/zeusCore/img/icons/famfamfam/cross.png" alt=""> <p class="zeus-button-label">Vertaling verwijderen</p></div></div>';
    
    return $ret;
  }
}