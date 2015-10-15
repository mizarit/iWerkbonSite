<?php

function mailing_edit($object, $field, $config = array())
{
  ob_start();
  ?>
<input type="hidden" name="send" id="send" value="no">
<input type="hidden" name="sendmode" id="sendmode" value="">
<input type="hidden" name="sendmodevalue" id="sendmodevalue" value="">
<div class="form-row">
  <div class="form-label"><label for="mode">Mode</label></div>
  <select name="mode" id="mode" onchange="checkMode(this.value);">
    <option value="draft">Ontwerp</option>
    <option value="send">Verzenden</option>
  </select>
</div>

<div class="form-row">
  <div class="form-label"><label for="mailinglist">Nieuwsbrieflijsten</label></div>
<?php
$c = new Criteria;
$c->addAscendingOrderByColumn(MailinglistPeer::TITLE);
$mailinglists = MailinglistPeer::doSelect($c);
$selected = explode('|', $object->getMailinglist());
foreach ($mailinglists as $mailinglist) {
  $checked = in_array($mailinglist->getId(), $selected) ? ' checked="checked"' : '';
  $id = 'mailinglist_'.$mailinglist->getId();
  echo '<input '.$checked.' class="checkbox" type="checkbox" id="'.$id.'" name="'.$id.'"> <label for="'.$id.'">'.$mailinglist->getTitle().'</label> ';
}
?>
</div>

<div class="form-row">
  <div class="form-label"><label for="site_site1">Afzender</label></div>
<?php
$cfg = zeusYaml::load(sfConfig::get('sf_root_dir').'/apps/frontend/config/app.yml');
$sites = $cfg['all']['multisite']['cms'];
$first = true;
foreach ($sites as $key => $site) {
  if (!$object->getSite()) {
    $checked = $first ? ' checked="checked"' : '';
  }
  else {
    $checked = $key == $object->getSite() ? ' checked="checked"' : '';
  }
  
  $id = 'site_'.$key;
  $first = false;
  echo '<input '.$checked.' class="checkbox" type="radio" id="'.$id.'" name="site" value="'.$key.'"> <label for="'.$id.'">'.$site['title'].'</label> '; 
}
?>
</div>

<div class="form-row">
  <div class="form-label"><label for="template">Template</label></div>
<?php
$v = $object->getTemplate();
if (!$v) $v = 'Default';
$templates = glob(sfConfig::get('sf_app_dir').'/modules/mailingadmin/templates/_mailing*.php');

$tmp = array();
foreach ($templates as $template) {
  $f = basename($template);
  $f = substr($f,8);
  $f = substr($f, 0, -4);
  $tmp[$f] = $f;
}

sfContext::getInstance()->getConfiguration()->loadHelpers(array('Form'));

echo select_tag('template', options_for_select($tmp, $v));
  ?>
</div>

<script type="text/javascript">
function checkMode(mod)
{
  if (mod == 'draft') {
    $('mailing-send-btn').addClassName('zeus-button-disabled');
  }
  else {
    $('mailing-send-btn').removeClassName('zeus-button-disabled');
    
  }
}

function sendMailing()
{
  $('send').value = 'yes';
  $('zeus-1').submit()
}

function previewMailing()
{
  $('sendmode').value = 'preview';
  $('sendmodevalue').value = '';
  $('zeus-1').submit();
}

function sendTestmailing()
{
  email = prompt('Naar welk e-mail adres wilt u de test verzenden?', '<?php echo zeusConfig::get('Nieuwsbrieven', 'Standaard beheerder e-mail', 'input', 'info@'.$_SERVER['HTTP_HOST']); ?>');
  if (email) {
    $('sendmode').value = 'test';
    $('sendmodevalue').value = email;
    $('zeus-1').submit();
  }
}

<?php if (sfContext::getInstance()->getRequest()->getParameterHolder()->has('send')) { ?>
alert('De testnieuwsbrief is verzonden');
<?php } ?>
</script>
  <?php
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Verzenden', 
    'icon'  => 'mail_forward',
    'type'  => 'large', 
    'id'    => 'mailing-send-btn',
    'disabled' => true,
    'callback' => "sendMailing()"
  )), 'Verzenden');
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Bekijken', 
    'icon'  => 'mail_generic',
    'type'  => 'large', 
    'id'    => 'mailing-preview-btn',
    'callback' => "previewMailing()"
  )), 'Verzenden');
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Proefzending', 
    'icon'  => 'mail_foward',
    'type'  => 'large', 
    'id'    => 'mailing-test-send-btn',
    'callback' => "sendTestmailing()"
  )), 'Verzenden');
  
  zeusRibbon::addButton(new zeusRibbonButton(array(
    'label' => 'Kopieër als nieuw', 
    'path'  => url_for(sfContext::getInstance()->getRequest()->getParameter('module').'/copy?id='.$object->getId()),
    'icon'  => 'tab_duplicate',
    'type'  => 'large',
    'id'    => 'copy-btn',
    'callback' => "window.open('".url_for(sfContext::getInstance()->getRequest()->getParameter('module').'/copy?id='.$object->getId())."');"
  )));
  return ob_get_clean();
}

function mailing_list_status($object, $config = array())
{
  $map['draft'] = 'Ontwerpen';
  $map['pending'] = 'Wacht op verzenden';
  $map['sending'] = 'Bezig met verzenden';
  $map['sent'] = 'Verzonden';
  $map['send'] = 'Verzonden';
  
  return $map[$object->getStatus()];
}