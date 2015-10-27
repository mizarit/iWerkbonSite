<?php

function zeus_edit($object = null, $config = array())
{
  static $cnt = 1;
  $form_name = 'zeus-'.$cnt;
  $cnt++;
  
  if (get_class($object) == 'sfOutputEscaperObjectDecorator') {
    $object = $object->getRawValue();
  }
  
  $i18n_html = $properties_html = '';
  
  use_helper('Form');
  
  zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Geschiedenis', 'id' => 'history-btn', 'disabled' => false, 'icon' => 'history')), 'Eigenschappen');
	zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Rechten', 'id' => 'permissions-btn', 'disabled' => false, 'icon' => 'locked')), 'Eigenschappen');
	zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Eigenschappen', 'id' => 'properties-btn', 'disabled' => false, 'icon' => 'advanced')), 'Eigenschappen');

	
  if (isset($config['buttons'])) {
	  foreach ($config['buttons'] as $button => $cfg) {
	    if ($cfg == 'disable') {
	      if (isset($buttons[$button])) unset($buttons[$button]);
	    }
	    
	    if (is_array($cfg)) {
	      if (isset($cfg['helper'])) {
	        use_helper($cfg['helper']);
	        //
	        $btn = $cfg['method']($cfg);
	        if (isset($cfg['toolbar'])) {
	          zeusRibbon::addButton($btn, $cfg['toolbar']);
	        }
	        else {
	          $buttons[$button] = $cfg['method']($cfg);
	        }
	      }
	    }
	  }
	}
	
	use_helper('ZeusProperties');
	$properties_html = zeus_properties($object);
	
	use_helper('ZeusHistory');
	$history_html = zeus_history($object);
	
	if (class_exists(get_class($object).'I18N')) {
	  $buttons['i18n'] = new zeusRibbonButtonI18N(array('culture' => sfConfig::get('sf_default_culture'), 'cultures' => sfConfig::get('sf_enabled_cultures') ));
	}
	
  $buttons['back'] = new zeusRibbonButtonBack(array('path' => sfContext::getInstance()->getRequest()->getParameter('module').'/index'));
  if ($object->getId()) {
    $buttons['delete'] = new zeusRibbonButtonDelete(array('object' => $object, 'path' => sfContext::getInstance()->getRequest()->getParameter('module').'/delete'));
  }
  if (!isset($config['buttons']['save']['disable'])) {
    $buttons['save'] = new zeusRibbonButtonSave(array('form' => $form_name)); 
  }

  if (isset($config['buttons'])) {
    foreach ($config['buttons'] as $button => $cfg) {
      if ($cfg == 'disable') {
        if (isset($buttons[$button])) unset($buttons[$button]);
      }
    }
  }
  
	
  $ret = '';
  $i18n_values = array();
  $i18n_types = array();
  
  $popups = array();
  
  $ret .= form_start($form_name, $object, $config);
  foreach ($config['fields'] as $field => $cfg)
  {
    $tmp = '';
    $value = '';
    if (isset($cfg['type']) && is_array($cfg['type']) && isset($cfg['type']['helper'])) {
      use_helper($cfg['type']['helper']);
      $tmp .= $cfg['type']['method']($object, $cfg);
    }
    else if(!isset($cfg['type']) || !in_array($cfg['type'], array('linklist', 'images', 'label', 'link1ton', 'textpartial'))) {
      $getter = 'get'.ucfirst($field);
      if (method_exists($object, $getter)) {
        $value = $object->$getter();
      }
      else if (isset($cfg['value'])) {
        $value = $cfg['value'];
      }
      
      if (sfContext::getInstance()->getRequest()->hasParameter($field)) {
        $value = sfContext::getInstance()->getRequest()->getParameter($field);
      }
      
      if (class_exists(get_class($object).'I18N')) {
        // i18n object
        if(@constant(get_class($object).'I18NPeer::'.strtoupper($field))) {
          // i18n object and this field is in the i18n table
          $cultures = sfConfig::get('sf_enabled_cultures');
          
          $current_culture = $object->getCulture();
          foreach ($cultures as $culture) {
            $object->setCulture($culture);
            $i18n_values[$field][$culture] = html_entity_decode($object->$getter());
            $i18n_types[$field] = $cfg['type'];
          }
          
          $object->setCulture($current_culture);
        }
      }
      $type = isset($cfg['type']) ? $cfg['type'] : 'input';
      $tmp .= form_row($field, call_user_func_array('zeus_edit_'.$type, array($object, $field, $value, $cfg)), $cfg);
    }
    else if($cfg['type'] == 'label') {
      $tmp .= zeus_edit_label($object, $field, false, $cfg);
    }
    else if($cfg['type'] == 'textpartial') {
      $tmp .= zeus_edit_textpartial($object, $field, false, $cfg);
    }
    else {
      $type = isset($cfg['type']) ? $cfg['type'] : 'input';
      $tmp .= form_row($field, call_user_func_array('zeus_edit_'.$type, array($object, $field, $value, $cfg)), $cfg);
    }
    
    if (isset($cfg['popup'])) {
      if (!isset($popups[$cfg['popup']])) $popups[$cfg['popup']] = '';
      $popups[$cfg['popup']] .= $tmp;
    }
    else {
      $ret .= $tmp;
    }
  }
  
  if (isset($config['popup'])) {
    $response = sfContext::getInstance()->getResponse();
    $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
  	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
  	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
  	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
  	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
    $response->addJavascript('/js/zeus-advanced/zeus-advanced.js');
 
    ob_start();
  ?>
  <div id="advanced-win" class="x-hidden">
    <div class="x-window-header">Geavanceerd</div>
    <div id="advanced-tabs">
    <?php foreach ($config['popup'] as $popup => $title) { ?>
      <div class="x-tab" title="<?php echo $title; ?>">
      <?php echo $popups[$popup]; ?>
      </div>
    <?php } ?>
    </div>
  </div>
  <script type="text/javascript">
  var advanced = new zeusAdvanced({
  
  });
  </script>
  <?php
    $ret .= ob_get_clean(); 
    zeusRibbon::addButton(new zeusRibbonButton(array('label' => 'Geavanceerd', 'id' => 'advanced-btn', 'disabled' => false, 'icon' => 'advanced')), 'Overig');
  }
  $request = sfContext::getInstance()->getRequest();
  
  if (count($i18n_values) > 0) { 
    ob_start(); ?>
<script type="text/javascript">
var i18n = null;
Event.observe(window, 'load', function() {
  i18n = new zeusI18N({
    culture: '<?php echo $request->hasParameter('culture') ? $request->getParameter('culture') : sfConfig::get('sf_default_culture'); ?>',
    default_culture: '<?php echo sfConfig::get('sf_default_culture'); ?>',
    cultures: <?php echo json_encode(sfConfig::get('sf_enabled_cultures')); ?>,
    types: <?php echo json_encode($i18n_types); ?>,
    data: <?php echo json_encode($i18n_values); ?>,
    form: '<?php echo $form_name ?>'
    
  }); 
});
</script>
    <?php
    $response = sfContext::getInstance()->getResponse();
	  $response->addJavascript('/zeusCore/js/zeus-i18n/zeus-i18n.js');
	  
	  $i18n_html = ob_get_clean();
  }
  
  $ret .= $properties_html;
  $ret .= $history_html;
  $ret .= $i18n_html;
  
  if ($object->getId()) {
    //$cfg = zeusYaml::load('admin.yml');
    //if (isset($cfg['list'])) {
    //  zeusFavorites::register('Bewerken van '.$cfg['list']['name']." \'".$object->getTitle()."\'", sfContext::getInstance()->getRequest()->getParameter('module').'/edit?id='.$object->getId());
    //}
  }
  
  $ret .= form_end();
  
  foreach ($buttons as $key => $button) {
    if ($key == 'i18n') {
      zeusRibbon::addButton($button, 'Meertaligheid');
    }
    else {
      zeusRibbon::addButton($button);
    }
	}
	
	echo '<div id="list-loader"></div>';
	
  return $ret;
}

function form_start($form_name = 'zeus', $object, $cfg)
{
  $ret = <<<EOT
<form action="{$cfg['update_url']}" method="post" id="{$form_name}">
  <fieldset>
    <legend>Zeus generated form</legend>\n
EOT;

  if ($object) {
    $id = $object->getId();
    $ret .= <<<EOT
    <input type="hidden" name="id" value="{$id}">\n
EOT;
  }
  
  return $ret;
}

function form_end()
{
  return <<<EOT
  </fieldset>
</form>\n
EOT;
}

function form_row($field_id, $field, $cfg)
{
  if (isset($cfg['type']) && $cfg['type'] == 'hidden') {
    return $field;
  }
  
  $help = $hint = '';
  
  if (isset($cfg['help'])) {
    $help = ' <img src="/zeusCore/img/icons/famfamfam/help.png" alt="'.$cfg['help'].'" title="'.$cfg['help'].'">';
  }

  if (isset($cfg['hint'])) {
    $hint = '<p class="hint">'.$cfg['hint'].'</p>';
  }
  
  $required = isset($cfg['required']) ? ' *' : '';
  
  if (!isset($cfg['label'])) $cfg['label'] = '';
  $ret = <<<EOT
<div class="form-row">
  <div class="form-label">
    <label for="{$field_id}" id="label-{$field_id}">{$cfg['label']}{$required}{$help}</label>
  </div>
  {$field}
  {$hint}
</div>
EOT;
  
  return $ret;

}

function zeus_edit_link1ton($object, $field, $value, $cfg)
{
  $ret = '';
  $c = new Criteria;
  $thismodel = method_exists($object, 'getRawValue') ? get_class($object->getRawValue()) : get_class($object);
  $model = ucfirst($cfg['table']);
  $modelpeer = $model . 'Peer';
  $linkpeer = $thismodel.$model.'Peer';
  $c->addAscendingOrderByColumn(constant($modelpeer.'::TITLE'));
  $linkables = call_user_func_array(array($modelpeer, 'doSelect'), array($c));
  foreach ($linkables as $linkable) {
    $c->clear();
    $c->add(constant($linkpeer.'::'.strtoupper($thismodel).'_ID'), $object->getId());
    $c->add(constant($linkpeer.'::'.strtoupper($model).'_ID'), $linkable->getId());
    $linked = call_user_func_array(array($linkpeer, 'doSelectOne'), array($c));
    $checked = $linked ? ' checked="checked"' : '';
    $id = $field.'-'.$linkable->getId();
    $ret .= '<input '.$checked.' class="checkbox" type="checkbox" id="'.$id.'" name="'.$id.'"> <label for="'.$id.'">'.$linkable->getTitle().'</label>';
  }
  
  return $ret;
}

function zeus_edit_input($object, $field, $value, $cfg)
{
  $w = isset($cfg['width']) ? $cfg['width'] : 300;
  $style['width'] = $w.'px';
  if (isset($cfg['style'])) {
    foreach ($cfg['style'] as $k => $v) {
      $style[$k] = $v;
    }
  }
  
  $style_str = '';
  foreach ($style as $k => $v) {
    $style_str.=$k.':'.$v.';';
  }
  $options = array('style' => $style_str);
  if (isset($cfg['readonly'])) {
    $options['readonly'] = 'readonly';
  }
  if (isset($cfg['disabled'])) {
    $options['disabled'] = 'disabled';
  }
  if (isset($cfg['onchange'])) {
    $options['onchange'] = $cfg['onchange'];
  }
  return input_tag($field, $value, $options);
}

function zeus_edit_price($object, $field, $value, $cfg)
{
  $w = isset($cfg['width']) ? $cfg['width'] : 300;
  return input_tag($field, number_format($value, 2, ',', '.'), array('style' => "width:{$w}px;"));
}

function zeus_edit_rich($object, $field, $value, $cfg)
{
  use_helper('Url');
  sfContext::getInstance()->getConfiguration()->loadHelpers('Files', 'filesadmin');
  // sfLoader::loadHelpers();
  
  $w = isset($cfg['width']) ? $cfg['width'] : zeusConfig::get('Page layout', 'Page width', 'input', 550);
  
  if (!isset($cfg['no_filebrowser'])) {
    files_browser();
    $tinymce_options = 'relative_urls: false, document_base_url: "http://plekjevrij.nl", convert_urls : false,remove_script_host : false, content_css: "/css/tinymce-screen.css", plugins: "paste,media,table,advimage,advlink,flash,zeusform", theme_advanced_blockformats: "p,h1,h2,h3", entity_encoding : "raw", language:"nl", width:'.$w.', height:320, debug:true, theme_advanced_buttons1: "zeusform,justifyleft,justifycenter,justifyright,justifyfull,separator,bold,italic,strikethrough,separator,sub,sup,separator,charmap,separator,tablecontrols", theme_advanced_buttons2: "formatselect,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,image,media,flash,separator,cleanup,removeformat,separator,code,separator,pasteword", theme_advanced_buttons3:"", skin:"o2k7", skin_variant:"silver", file_browser_callback:"zeusFileBrowser", external_link_list_url:"'.url_for('linklist/json').'"';
  }
  else {
    $tinymce_options = 'relative_urls: false, document_base_url: "http://plekjevrij.nl", convert_urls : false,remove_script_host : false, content_css: "/css/tinymce-screen.css", plugins: "paste,media,table,advlink,flash,zeusform", theme_advanced_blockformats: "p,h1,h2,h3", entity_encoding : "raw", language:"nl", width:'.$w.', height:320, debug:true, theme_advanced_buttons1: "zeusform,justifyleft,justifycenter,justifyright,justifyfull,separator,bold,italic,strikethrough,separator,sub,sup,separator,charmap,separator,tablecontrols", theme_advanced_buttons2: "formatselect,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,media,flash,separator,cleanup,removeformat,separator,code,separator,pasteword", theme_advanced_buttons3:"", skin:"o2k7", skin_variant:"silver", external_link_list_url:"'.url_for('linklist/json').'"';
  }
 
  
  return textarea_tag($field, $value, array('tinymce_options' => $tinymce_options, 'rich' => true, 'rows' => 40, 'cols' => 40));
}

function zeus_edit_text($object, $field, $value, $cfg)
{
  $w = isset($cfg['width']) ? $cfg['width'] : 544;
  $h = isset($cfg['height']) ? $cfg['height'] : 50;
  return textarea_tag($field, $value, array('rows' => 40, 'cols' => 40, 'style' => "width:{$w}px;height:{$h}px;"));
}

function zeus_edit_checkbox($object, $field, $value, $cfg)
{
  return checkbox_tag($field, 1, $value, array('class' => 'checkbox'));
}

function zeus_edit_hidden($object, $field, $value, $cfg)
{
  if (isset($cfg['value'])) {
    $value = $cfg['value'];
  }
  return input_hidden_tag($field, $value, $cfg);
}

function zeus_edit_time($object, $field, $value, $cfg)
{
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/js/timepicker/Proto.TimePicker.css');
	$response->addJavascript('/js/timepicker/Proto.TimePicker.js');
	$value = $value != '' ? $value : '09:00';
	
	$js = <<<EOT
<script type="text/javascript">
new Proto.TimePicker('{$field}', {
	startTime:   '00:00',
	endTime:     '23:59',
	show24Hours: true,
	separator:   ':',
	step:        10
});

$('{$field}').on('keyup', function() {
	validateTimeField({$field});
});

$('{$field}').on('time:change', function() {
	validateTimeField({$field});
});
</script>\n
EOT;
	return input_tag($field, $value, array('style' => 'width:50px')).$js;
}


function zeus_edit_colorpicker($object, $field, $value, $cfg)
{
  $response = sfContext::getInstance()->getResponse();
	$response->addStylesheet('/js/colorpickerjs-1.0/colorPicker.css');
	$response->addJavascript('/js/colorpickerjs-1.0/yahoo.color.js');
	$response->addJavascript('/js/colorpickerjs-1.0/colorPicker.js');
	if ($value == '') $value = 'AA1E1E';
	
	$js = <<<EOT
<script type="text/javascript">
new Control.ColorPicker('{$field}', {IMAGE_BASE: '/js/colorpickerjs-1.0/img/', swatch: 'swatch-{$field}'});
</script>\n
EOT;
	return input_tag($field, $value, array('style' => 'width:50px')).'<div id="swatch-'.$field.'" class="swatch"></div>'.$js;
}

function zeus_edit_date($object, $field, $value, $cfg)
{
  $config = array('rich' => true, 'culture' => 'nl_NL', 'readonly' => true);
  
  if (isset($cfg['withtime'])) {
    $config['withtime'] = $cfg['withtime'];
  }
  
  $numericalTime = @strtotime($value);
  if ($numericalTime === false) {
    $value = null;
  }
  $datepicker = input_date_tag($field, $value, $config);
  
  return str_replace('...</button>', '<div>...</div></button>', $datepicker);
}

function zeus_edit_select($object, $field, $value, $cfg)
{
  $w = isset($cfg['width']) ? $cfg['width'] : 306;
  
  $options = options_for_select($cfg['options'], $value);
  
  $cfg_select['style'] = "width:{$w}px;";
  
  if (isset($cfg['callback'])) {
    foreach ($cfg['callback'] as $k => $v) {
      $cfg_select[$k] = $v;
    }
  }
  return select_tag($field, $options, $cfg_select);
}

function zeus_edit_images($object, $field, $value, $cfg)
{
  
}

function zeus_edit_image($object, $field, $value, $cfg)
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Files', 'filesadmin');
  
  $width = isset($cfg['width']) ? $cfg['width'] : 64;
  $height = isset($cfg['height']) ? $cfg['height'] : 64;
  
  $valid = true;
  
  if (strlen($value) > 0) {
    $parts = explode('.', $value);
    $ext = strtolower(array_pop($parts));
    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
      $valid = false;
    }
  }
  ob_start();
?>
  <div class="image-preview" id="image-preview-<?php echo $field; ?>" style="float: left; margin-right: 5px;">
<?php if (strlen($value) > 0 && $valid) { ?>
    <img src="<?php echo zeusImages::getPresentation($value, array('root' => sfConfig::get('sf_root_dir').'/agenda', 'width' => $width, 'height' => $height, 'resize_method' => zeusImages::RESIZE_CHOP)); ?>" alt="">
    <span onclick="$('image-preview-<?php echo $field; ?>').innerHTML = '';$('<?php echo $field; ?>').value = '';" title="Afbeelding verwijderen" class="image-delete"></span>
<?php } elseif(!$valid) { ?>
Niet geldige afbeelding. Upload alleen bestanden van het type jpg, png of gif.
<?php } ?>
  </div>
  <input type="hidden" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $value; ?>">
  <?php echo files_upload_button_simple($field, $value, $cfg); ?>
  <div style="clear:both;"></div>
<?php
  return ob_get_clean();
}

function zeus_edit_file($object, $field, $value, $cfg)
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Files', 'filesadmin');
  sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
  ob_start();
  $cfg['update_url'] = url_for('filesadmin/updatefile');
  $cfg['update_container'] = 'file-upload-'.$field;
  $cfg['update_handler'] = 'uploadCompleteSimple';
  
  $cls = $value != '' ? ' has-file' : '';
  $value = basename($value);
  ?>
  <div class="file-upload">
    <div id="file-upload-<?php echo $field; ?>" class="file-upload-container<?php echo $cls; ?>"><div id="file-upload-<?php echo $field; ?>-value"><?php echo $value; ?></div><span onclick="updateDeleteFile('<?php echo $field; ?>');"></span></div>
    <div style="clear:both;"></div>
    <input type="hidden" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $value; ?>">
    <?php echo files_upload_button_simple($field, $value, $cfg); ?>
  </div>
  <div style="clear:both;"></div>
  
<?php
  return ob_get_clean();
}

function zeus_edit_label($object, $field, $value, $cfg)
{
  return '<div class="form-row"><h4>'.$cfg['value'].'</h4></div>';
}

function zeus_edit_textpartial($object, $field, $value, $cfg)
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
  
  $c = new Criteria;
  $c->add(PageI18NPeer::TITLE, $cfg['key']);
  $page = PagePeer::doSelectWithI18N($c, 'nl_NL');
  
  
  $class = isset($cfg['cls']) ? $cfg['cls']: 'admin-helper';
  
  $host = str_replace('cms.', '', $_SERVER['HTTP_HOST']);
  $host = str_replace('www.', '', $host);
  if ($page) {
    $page = array_shift($page);
    $ret = $page->getContent();
    
    // TODO: test for admin
    if (sfContext::getInstance()->getUser()->isAuthenticated()) {
      $url = url_for('partialadmin/edit?id='.$page->getId());
      $ret .= "<p class=\"{$class}\">Tekst '{$cfg['key']}', <a href=\"http://cms.{$host}{$url}\" target=\"_blank\">tekst aanpassen</a></p>";
    }
    return $ret;
  }
  
  if (sfContext::getInstance()->getUser()->isAuthenticated()) {
    $url = url_for('partialadmin/create?title='.$cfg['key']);
    return "<p class=\"{$class}\">Tekst bestaat niet: '{$cfg['key']}', <a href=\"http://cms.{$host}{$url}\" target=\"_blank\">tekst aanmaken</a></p>";
  }
  
  return '';
}
 
function zeus_edit_linklist($object, $field, $value, $cfg)
{
  
  $table1 = get_class($object->getRawValue());
  $table2 = ucfirst($cfg['options']['relation']);
  $table2peer = $table2.'Peer';
  
  $linktable = $table1.$table2;
  $linktablepeer = $linktable.'Peer';
  
  $linkedids = array();
  $linkedlist = array();
  
  $c = new Criteria;
  $c->add(constant($linktablepeer.'::'.strtoupper($table1). '_ID'), $object->getId());
  $linked = call_user_func_array(array($linktablepeer, 'doSelect'), array($c));
  $getter = 'get'.$table2.'Id';
  foreach ($linked as $link) {
    $linkedids[] = $link->$getter();
  }
  
  if (!isset($cfg['options']['nested'])) {
    $c = new Criteria;
    $c->addAscendingOrderByColumn(constant($table2peer.'::TITLE'));
    $objects = call_user_func_array(array($table2peer, 'doSelect'), array($c));
    foreach ($objects as $lobject) {
      $values[$lobject->getId()] = $lobject->getTitle();
    }
  }
  else {
    $c = new Criteria;
    $c->add(constant($table2peer.'::TREE_PARENT'), null, Criteria::ISNULL);
    $root = call_user_func_array(array($table2peer, 'doSelectOne'), array($c));
    if ($root) {
      foreach ($root->getChildren() as $child) {
        $values[$child->getId()] = $child->getTitle();
        
        if ($child->hasChildren()) {
          foreach ($child->getChildren() as $schild)
          {
            $values[$schild->getId()] = '--- '.$schild->getTitle();
          }
        }
      }
    }
  }
  
  foreach ($linkedids as $linkedid) {
    unset($values[$linkedid]);
    $linked = call_user_func_array(array($table2peer, 'retrieveByPk'), array($linkedid));
    
    $str = $linked->getTitle();
    if ($linked->getLevel() == 2) {
      $parent = $linked->getParent();
      $str = $parent->getTitle() . ' - ' . $str;
    }
    $linkedlist[$linkedid] = $str;
  }
  
  $content = select_tag('linklist', options_for_select($values), array('style' => 'width:300px;'));
  $content .= '<button onclick="linkList.add(this);" type="button"><div>Toevoegen</div></button>';
  $content .= '<div style="margin-left:150px;" class="linklist"><ul>';
  
  foreach ($linkedlist as $id => $linkedlistobject) {
    
    
    $content .= '<li>'.$linkedlistobject. '<span onclick="linkList.delete('.$id.');"></span></li>';
  }
  
  $content .= '</ul></div>';
  return $content;
}

function zeus_edit_smartcheckbox($object, $field, $value, $cfg)
{
  return '<img src="/zeusCore/img/checkbox/check-on.png" alt="">';
}
