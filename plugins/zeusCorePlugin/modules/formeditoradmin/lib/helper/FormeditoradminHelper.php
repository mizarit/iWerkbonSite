<?php

function edit_formeditoradmin_create_trigger()
{
  ob_start();
  ?>
<script type="text/javascript">
var env = '<?php echo sfConfig::get('sf_environment') == 'dev' ? '/backend_dev.php' : '/backend.php'; ?>';

var editorurl = '<?php echo url_for('formeditoradmin/form'); ?>';

var selectedRow = 0;

function formeditoradmin_addrow(v)
{
  new Ajax.Updater('formeditor-container', '<?php echo url_for('formeditoradmin/form'); ?>', {
    parameters: {
      rtype: v
    },
    evalScripts: true
  });
}

function formeditoradmin_deleterow(v)
{
  new Ajax.Updater('formeditor-container', '<?php echo url_for('formeditoradmin/form'); ?>', {
    parameters: {
      deleterow: v
    },
    evalScripts: true
  });
  
  selectedRow = false;
  
  $('delete-row-btn').addClassName('zeus-button-disabled');
  $('validator-row-btn').addClassName('zeus-button-disabled');
}

function formeditoradmin_editrow(v)
{
  
}
</script>
<?php
  return ob_get_clean();
}

function edit_formeditoradmin_rows($object, $config = array())
{
  $frows = array();
  
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SlidingPager.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SliderTip.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PanelResizer.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PagingMemoryProxy.js');
  $response->addJavascript('/zeusCore/js/zeus-formeditor/zeus-formeditor.js');
  
  $c = new Criteria;
  $c->addAscendingOrderByColumn(FormrowPeer::TABINDEX );
  $c->add(FormrowPeer::FORM_ID, $object->getId());
  $rows = FormrowPeer::doSelect($c);
  foreach ($rows as $row) {
    $frows[] = array(
      'type' => $row->getRtype(),
      'label' => $row->getTitle(),
      'rvalidator' => $row->getRvalidator(),
      'rvalidatorvalue' => $row->getRvalidatorvalue(),
      'rvalue' => $row->getRvalue(),
      'roptions' => $row->getRoptions(),
      'rrequired' => $row->getRrequired()
    );
  }
  
  sfContext::getInstance()->getUser()->setAttribute('formrows', $frows);
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Verwijderen', 
    'path'  => '',
    'icon'  => 'fileclose',
    'type'  => 'large',
    'callback' => 'formeditoradmin_deleterow(selectedRow)',
    'id'    => 'delete-row-btn',
    'disabled' => true
  )), 'Velden');
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Eigenschappen', 
    'path'  => '',
    'icon'  => 'gear',
    'type'  => 'large',
    'callback' => 'formeditoradmin_editrow(selectedRow)',
    'id'    => 'validator-row-btn',
    'disabled' => true
  )), 'Velden');
  
  
  zeusRibbon::addButton(new zeusRibbonButtonCreate(array(
    'label' => 'Toevoegen', 
    'path'  => '',
    'icon'  => 'new_window',
    'type'  => 'large-pulldown',
    'id'    => 'add-row-btn',
    'pulldown' => array(
      'title' => 'Veld toevoegen',
      'options' => edit_formeditoradmin_rtypes(),
      'callback' => 'formeditoradmin_addrow',
      'parameters' => $object->getId(),
      'default' => 'input'
    )
  )), 'Velden');
  
  ob_start();
  ?>
   <div id="formeditor-win" class="x-hidden">
    <div class="x-window-header">Eigenschappen</div>
    <div id="formeditor-tabs">
      <div class="x-tab" title="Waarden">
        <div class="form-row" id="form-row-default-value">
         <div class="form-label"><label for="form-default-value">Standaard waarde</label></div>
         <input style="padding:2px;width: 300px;" value="" type="text" name="form-default-value" id="form-default-value">
        </div>
        
        <div class="form-row" id="form-row-options">
         <div class="form-label"><label for="form-options">Opties</label></div>
         <textarea style="padding:2px;width: 300px;" name="form-options" id="form-options"></textarea>
        </div>
        
      </div>
      
      <div class="x-tab" title="Validatie">
        <div class="form-row" id="form-row-required">
          <div class="form-label"><label for="form-required">Verplicht veld</label></div>
          <input type="checkbox" class="checkbox" id="form-required" name="form-required">
        </div>
        <div class="form-row" id="form-row-validator">
          <div class="form-label"><label for="form-validator">Validatie methode</label></div>
          <select style="padding:2px;width: 300px;" name="form-validator" id="form-validator">
            <option>Minimale lengte</option>
            <option>Maximale lengte</option>
            <option>Exacte lengte</option>
            <option>Postcode</option>
            <option>E-mail adres</option>
            <option>Telefoonnummer</option>
            <option>Reguliere expressie</option>
          </select>
        </div>
        
        <div class="form-row" id="form-row-validator-value">
          <div class="form-label"><label for="form-validator-value">Validatie waarde</label></div>
          <input type="text" style="padding:2px;width: 294px;" name="form-validator-value" id="form-validator-value">
        </div>
        
        
      </div>
    </div>
  </div>
  
  <div id="formeditor-container"><?php echo edit_formeditoradmin_render($frows); ?></div>
  
<script type="text/javascript">
var zformeditor = new zeusFormeditor({});


</script>
  <?php
  echo edit_formeditoradmin_create_trigger();
  return ob_get_clean();
}

function edit_formeditoradmin_rtypes()
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Tag');
  sfContext::getInstance()->getConfiguration()->loadHelpers('Form');
  $rtypes['input'] = input_tag('row1', '',  array('style' => 'width:300px;margin:5px 0;'));
  $rtypes['textarea'] = textarea_tag('row2', '',  array('style' => 'width:300px;margin:5px 0;'));
  $rtypes['select'] = select_tag('row3', '',  array('style' => 'width:300px;margin:5px 0;'));
  $rtypes['checkbox'] = checkbox_tag('row4', '1', '',  array('class' => 'checkbox', 'style' => 'margin:5px 0;'));
  $rtypes['radio'] = radiobutton_tag('row5', 'v1', '',  array('class' => 'checkbox', 'style' => 'margin:5px 0;'));
  
  return $rtypes;
}

function edit_formeditoradmin_render($rows)
{
  ob_start();
  ?>
  <h3>Formuliervelden</h3>
<ul id="formeditor-rows">
<?php



foreach ($rows as $key => $row) { 
  
  echo '<li id="row_'.$key.'" onclick="activateRow(this);">';
  switch ($row['type']) {
    case 'input':
      echo form_row('row-'.$key, input_tag('row-'.$key, $row['rvalue'], array('style' => 'width:300px;')), array('label' => $row['label']));
      break;
      
    case 'textarea':
      echo form_row('row-'.$key, textarea_tag('row-'.$key, $row['rvalue'], array('style' => 'width:300px;')), array('label' => $row['label']));
      break;
      
    case 'radio':
      echo form_row('row-'.$key, radiobutton_tag('row-'.$key, '1', '', array('class' => 'checkbox')), array('label' => $row['label']));
      break;
      
    case 'checkbox':
      echo form_row('row-'.$key, checkbox_tag('row-'.$key, 'on', '', array('class' => 'checkbox')), array('label' => $row['label']));
      break;
      
    case 'select':
      $options = explode("\n", $row['roptions']);
      
      echo form_row('row-'.$key, select_tag('row-'.$key, options_for_select($options, $row['rvalue']), array('style' => 'width:306px;')), array('label' => $row['label']));
      break;
  }
  echo '</li>';
}

?>
</ul>
<script type="text/javascript">
<?php if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest()) { ?>
Event.observe(window, 'load', function() {
<?php } ?>
<?php foreach ($rows as $key => $row) { ?>
new Ajax.InPlaceEditor( 'label-row-<?php echo $key; ?>', '<?php echo url_for('formeditoradmin/form'); ?>', { 
  cancelText: 'annuleer',
  clickToEditText: 'Klik om te bewerken'
});
<?php } ?>

zformeditor.Options.rows = [
<?php 
$c = 0;
foreach ($rows as $key => $row) { 
  $c++;
  ?>
{ rtype: '<?php echo $row['type']; ?>', 
  label: '<?php echo $row['label']; ?>', 
  rvalidator: '<?php echo $row['rvalidator']; ?>', 
  rvalidatorvalue: '<?php echo $row['rvalidatorvalue']; ?>', 
  rvalue: '<?php echo $row['rvalue']; ?>', 
  roptions: '<?php echo $row['roptions']; ?>', 
  rrequired: <?php echo $row['rrequired'] ? 'true' : 'false'; ?> }
<?php 
  if ($c < count($rows)) echo  ',';
} ?>];

Sortable.create('formeditor-rows');

activateRow = function(what)
{
  $$('#formeditor-rows li').each(function(o, i) {
    o.removeClassName('active');
  });
  what.addClassName('active');
  
  
  selectedRow = what.id.substr(4);
  $('delete-row-btn').removeClassName('zeus-button-disabled');
  $('validator-row-btn').removeClassName('zeus-button-disabled');
}
<?php if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest()) { ?>
});
<?php } ?>
</script>
<?php

  return ob_get_clean();

}