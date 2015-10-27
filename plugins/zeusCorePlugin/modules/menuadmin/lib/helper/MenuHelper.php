<?php

function menu_saveorder_button($config = array())
{
  $response = sfContext::getInstance()->getResponse();
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addStylesheet('/zeusCore/css/zeus-columnnode-screen.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/zeus-treeview/zeus-treeview.js');
	
	return new zeusRibbonButton(array(
    'type'      => 'large',
    'label'     => 'Volgorde opslaan',
    'icon'      => 'filesave',
    'id'        => 'saveorder-btn',
    'disabled'  => true,
    'callback'  => "tree.saveOrder();"
  ));
}

function menu_container($cfg = array())
{
  return '<div id="menu-container"></div>';
}

function menu_target($object = null, $cfg = array())
{
  $value = $object->getValue();
  
  $selected = $object->getType() == '' ? 'intern' : $object->getType();
  
  $enabled_intern = $selected == 'intern' ? '' : ' style="display:none;"';
  $enabled_extern = $selected == 'extern' ? '' : ' style="display:none;"';
  $enabled_email  = $selected == 'email' ? '' : ' style="display:none;"';
  
  $selector = <<<EOT
<div id="selector-intern"{$enabled_intern}>
  <select name="link-intern" id="link-intern" style="width:306px;">
    <optgroup label="Pagina's">\n
EOT;
    
  use_helper('ZeusRoute');
  $c = new Criteria;
  $c->addAscendingOrderByColumn(PageI18NPeer::TITLE);
  $pages = PagePeer::doSelectWithI18N($c, sfContext::getInstance()->getUser()->getCulture());
  foreach ($pages as $page) {
    $value = 'Page:'.$page->getId();
    $title = $page->getTitle();
  
    $selector .= <<<EOT
      <option value="{$value}">{$title}</option>\n
EOT;
  }
  $selector .= <<<EOT
    </optgroup>\n
EOT;

  $actions = sfConfig::get('app_actionlinks_list');
  if ($actions) {
    $selector .= <<<EOT
    <optgroup label="Actiepagina's">\n
EOT;
  
    foreach ($actions as $action => $label) {
      $selector .= <<<EOT
      <option value="{$action}">{$label}</option>\n
EOT;
    }
    $selector .= <<<EOT
    </optgroup>\n
EOT;
  }
  $selector .= <<<EOT
  </select>
</div>
<div id="selector-extern"{$enabled_extern}>
  <input type="text" name="link-extern" id="link-extern" value="{$value}" style="width:300px;">
</div>
<div id="selector-email"{$enabled_email}>
  <input type="text" name="link-email" id="link-email" value="{$value}" style="width:300px;">
</div>
<script type="text/javascript">
function changeTarget(what)
{
  $('selector-intern').style.display = ( what.value == 'intern' ) ? 'block' : 'none';
  $('selector-extern').style.display = ( what.value == 'extern' ) ? 'block' : 'none';
  $('selector-email').style.display = ( what.value == 'email' ) ? 'block' : 'none';
}
</script>
\n
EOT;
  return form_row('link-intern', $selector, $cfg);
}