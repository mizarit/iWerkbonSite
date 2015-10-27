<?php

function formdata_create_trigger()
{
  ob_start();
  ?>
<script type="text/javascript">
var env = '<?php echo sfConfig::get('sf_environment') == 'dev' ? '/backend_dev.php' : '/backend.php'; ?>';

function formdata_export(v, id)
{
  window.location.href = env + '/<?php echo sfContext::getInstance()->getRequest()->getParameter('module'); ?>/export/type/' + v + '/object/' + id;
}''
</script>
<?php
  return ob_get_clean();
}

function formdata_export_button($config = array())
{
  echo formdata_create_trigger();
  
  zeusFavorites::register("Exporteren van alle formulieren naar Microsoft Office Excel 2003", '/formadmin/export/type/xlsx/object/undefined');
  zeusFavorites::register("Exporteren van alle formulieren naar Standard-compliant CSV", '/formadmin/export/type/ocsv/object/undefined');
  zeusFavorites::register("Exporteren van alle formulieren naar Microsoft Office Excel CSV", '/formadmin/export/type/csv/object/undefined');
  zeusFavorites::register("Exporteren van alle formulieren naar Open Office XML", '/formadmin/export/type/xml/object/undefined');
  
  return new zeusRibbonButtonCreate(array(
    'label' => 'Exporteren', 
    'path'  => sfContext::getInstance()->getRequest()->getParameter('module').'/export/type/csv',
    'icon'  => 'fileexport',
    'type'  => 'large-pulldown',
    'id'    => 'export-btn',
    'pulldown' => array(
      'title' => 'Kies een formaat',
      'options' => array(
        'xlsx' => 'Microsoft Office Excel 2003',
        'ocsv' => 'Standard-compliant CSV',
        'csv' => 'Microsoft Office Excel CSV',
        'xml' => 'Open Office XML'
      ),
      'callback' => 'formdata_export',
      'default' => 'xlsx'
    )
  ));
  
  
}

function formdata_view($object = null, $config = array())
{
  $ret = '';
  
  $title = $object->getTitle();
  
  // try to load the form config
  $config = 'form-'.$object->getTitle().'.yml';
  $files = glob(sfConfig::get('sf_root_dir').'/apps/frontend/modules/form/config/'.$config);
  if (!$files) {
    $files = glob(sfConfig::get('sf_plugins_dir').'/*/modules/*/config/'.$config);
  }
  
  if ($files) {
    $configfile = zeusYaml::load($files[0]);
    $title = $configfile['title'];
  }
  
  
  $ret .= '<div class="hr"></div>';
  $ret .= form_row('formname', '<span id="formname">'.$title.'</span>', array('label' => 'Formulier'));
  $ret .= form_row('formdate', '<span id="formdate">'.date('d-m-Y H:i:s', strtotime($object->getDate())).'</span>', array('label' => 'Verzonden op'));
  $ret .= '<div class="hr"></div>';
  
  $data = unserialize(html_entity_decode($object->getData()));
  foreach ($data as $key => $value) {
    if ($value == '') $value = '&nbsp;';
    
    $label = isset($configfile['fields'][$key]['label']) ? $configfile['fields'][$key]['label'] : $key;
    
    if ($configfile['fields'][$key]['type'] == 'file') {
      $value = '<a target="_blank" href="/uploads'.$value.'">Bestand downloaden</a>';
    }
    $ret .= form_row($key, '<span id="'.$key.'">'.$value.'</span>', array('label' => $label));
  }
  
  zeusRibbon::addButton(new zeusRibbonButtonCreate(array(
    'label' => 'Exporteren', 
    'path'  => sfContext::getInstance()->getRequest()->getParameter('module').'/export/form/'.$object->getId(),
    'icon'  => 'fileexport',
    'type'  => 'large-pulldown',
    'id'    => 'export-btn',
    'pulldown' => array(
      'title' => 'Kies een formaat',
      'options' => array(
        'xlsx' => 'Microsoft Office Excel 2003',
        'ocsv' => 'Standard-compliant CSV',
        'csv' => 'Microsoft Office Excel CSV',
        'xml' => 'Open Office XML'
      ),
      'callback' => 'formdata_export',
      'parameters' => $object->getId(),
      'default' => 'xlsx'
    )
  )));
  
  $ret .= formdata_create_trigger();
  
  return $ret;
}