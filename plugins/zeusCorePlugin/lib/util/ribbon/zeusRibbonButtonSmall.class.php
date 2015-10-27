<?php

class zeusRibbonButtonSmall extends zeusRibbonButton
{
  protected $config = array(
    'label'     => 'Small button',
    'type'      => 'small',
    'icon'      => 'generic-small',
    'callback'  => 'zeusRibbon.save'
  );
}