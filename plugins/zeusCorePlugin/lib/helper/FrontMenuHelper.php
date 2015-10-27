<?php


function frontmenu_get_url($menu)
{
  $url = '';
  switch($menu->getType()) {
    case 'intern':
      $url = route_for($menu->getValue());
      break;
    case 'extern':
      $url = $menu->getValue();
      break;
    case 'email': 
      $url = 'mailto:'.$menu->getValue();
      break;
      
    default:
      $url = '#';
      break;
  }
  
  return $url;
}