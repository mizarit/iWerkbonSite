<form action="<?php echo url_for('newsadmin/createfeed'); ?>" method="post" id="zeus-1">
  <fieldset>
    <legend>form</legend>
    <div class="form-row">
      <div class="form-label"><label for="url">Feed URL</label></div>
      <input type="text" name="url" id="url">
    </div>
  </fieldset>
</form>
<?php 
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Geschiedenis', 'id' => 'history-btn', 'disabled' => true, 'icon' => 'history')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Rechten', 'id' => 'permissions-btn', 'disabled' => true, 'icon' => 'locked')), 'Eigenschappen');
zeusRibbon::addButton(new zeusRibbonButtonSmall(array('label' => 'Eigenschappen', 'id' => 'properties-btn', 'disabled' => true, 'icon' => 'advanced')), 'Eigenschappen');

//zeusRibbon::addButton(new zeusRibbonButtonBack(array('path' => sfContext::getInstance()->getRequest()->getParameter('module').'/import')), 'Acties');

//sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

zeusRibbon::addButton(new zeusRibbonButton(array(
'label' => 'Terug naar feeds', 
'icon'  => 'previous',
'type'  => 'large', 
'id'    => 'import-news-btn',
'callback' => "window.location.href='".url_for('newsadmin/importfeeds')."'"
  )), 'Acties');
  
zeusRibbon::addButton(new zeusRibbonButton(array(
'label' => 'Opslaan', 
'icon'  => 'filesave',
'type'  => 'large', 
'id'    => 'new-feed-btn',
'callback' => "$('zeus-1').submit();"
  )), 'Acties');
  
include_component('core', 'helpers'); ?>