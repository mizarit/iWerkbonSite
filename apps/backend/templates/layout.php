<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
<?php 
include_http_metas();
include_metas();
$title = $sf_response->getTitle();
$sf_response->setTitle('Zeus4 - '.$title);
include_title();
$sf_response->setTitle($title);
include_stylesheets();
include_javascripts() 
?>
</head>
<div id="header">
<script type="text/javascript">
<?php 
$controller = sfConfig::get('sf_environment') == 'dev' ? 'backend_dev.php' : 'index.php';
echo "var zeusController = '/{$controller}';\n";

$plugins = array(
  'zeus-core' => 'app_backendmodules_zeus_core_list'
);

$active_plugin = 'CMS';
foreach ($plugins as $zeus_app => $plugin) {
  
  $modules = sfConfig::get($plugin);

  $active = false;
  foreach ($modules as $module => $label) {
    if ($sf_params->get('module') == $module && !is_array($label)) {
      $active = true;
    }
  }
  
  if ($active) {
    list($f, $app) = explode('-', $zeus_app);
    if ($app == 'core') $app = 'CMS';
    $active_plugin = $app;
    break;
  }
}

?>
</script>
<div id="logo">
  <h1><a href="<?php echo url_for('@homepage') ?>">Zeus<span>CMS4</span></a></h1>
<?php if ($sf_user->isAuthenticated()) { ?>
  <p id="top-icons">
    
    <a href="#" id="help-btn"><img src="/zeusCore/img/icons/famfamfam/help.png" alt="Help">Help</a> | 
    <a href="#" id="favorites-btn"><img src="/zeusCore/img/icons/famfamfam/star.png" alt="Favorieten">Favorieten</a> <a href="#" class="disabled" id="favorites-add-btn"><img id="favorites-add-btn-icon" src="/zeusCore/img/icons/famfamfam/add-off.png" alt="Favoriet toevoegen"></a> | 
    <a href="<?php echo url_for('settingsadmin/index') ?>"><img src="/zeusCore/img/icons/famfamfam/cog.png" alt="Instellingen">Instellingen</a> | 
    <a href="<?php echo url_for('securityadmin/logoff') ?>"><img src="/zeusCore/img/icons/icon-logoff.gif" alt="Uitloggen">Uitloggen</a>
    
  </p>
  <?php include_component('multisite', 'directlogin'); ?>
 <?php } ?>
</div>
<?php 

if (!$sf_user->isAuthenticated()) {
  $modules = array(
    'dashboardadmin' => 'Dashboard'
   );
	
}
else {
  foreach ($plugins as $zeus_app => $plugin) {
    
    $modules = sfConfig::get($plugin);

    $active = false;
    if (is_array($modules)) {
      foreach ($modules as $module => $label) {
        if ($sf_params->get('module') == $module && !is_array($label)) {
          $active = true;
        }
      }
    }
   
    if (is_array($modules)) { 
?>
<div style="clear:both;"></div>
<div class="nav <?php if ($active || in_array($sf_params->get('module'), array('useradmin', 'groupadmin', 'settingsadmin', 'permissionsadmin', 'formeditoradmin', 'mailingadmin', 'mailinguseradmin', 'mailinglistadmin'))) echo 'nav-active'; ?> <?php echo $zeus_app ?>">
  <ul>
<?php
 

    foreach ($modules as $module => $label) {
      $classes = array();
      if ($sf_params->get('module') == $module) {
        $classes[] = 'active';
      }
      
      if ($sf_params->get('module') == 'formeditoradmin' && $module == 'formadmin') {
        $classes[] = 'active';
      }
      
      if ($module == 'mailingadmin' && in_array($sf_params->get('module'), array('mailingadmin', 'mailinguseradmin', 'mailinglistadmin'))) {
        $classes[] = 'active';
      }
      
      if (is_array($label)) {
        if (isset($label['class'])) {
          $classes[] = $label['class'];
        }
        
        if (isset($label['label'])) {
          $label = $label['label'];
        }
      }
      
      $active = count($classes) > 0 ? ' class="'.implode(' ', $classes).'"' : '';
?>
    <li<?php echo $active?>><a href="<?php echo url_for($module.'/index') ?>"><?php echo $label ?></a></li>
<?php 
    } 
?>
  </ul>
</div>
<div style="clear:both;"></div>
  <?php
  }
  }
}
?>
</div>

<hr>
<?php echo zeusRibbon::get() ?>
<div id="container">
  <div id="container-inner">
  
<?php
for ($c = 0; $c < 10; $c++) {
  $sidebars = sfConfig::get('app_sidebars_sidebar'.$c);
  if (!$sidebars) continue;
  
  foreach ($sidebars as $sidebar) {
    foreach ($sidebar['items'] as $item) {
      if ($sf_params->get('module') == $item['module']) {
        $active_sidebar = sfConfig::get('app_sidebars_sidebar'.$c);
      }
    }
  }
}

if (isset($active_sidebar)) { ?>
    <div id="settings-sub-nav-container">
      <div id="settings-sub-nav-container-inner">
<?php foreach ($active_sidebar as $sidebar) { ?>
        <h2><?php echo $sidebar['label']; ?></h2>
        <ul>
<?php foreach ($sidebar['items'] as $item) { 
$selected = $sf_params->get('module') == $item['module'] ? 'active-item ' : ''; ?>
          <li class="<?php echo $selected; ?>icon-<?php echo $item['icon']; ?>"><a href="<?php echo url_for($item['module'].'/index'); ?>"><?php echo $item['label']; ?></a></li>
<?php } ?>
        </ul>
<?php } ?>
      </div>
    </div>
    <div id="settings-container-inner">
<?php } ?>
    
  <?php echo $sf_content ?>
    </div>
<?php if (isset($active_sidebar)) { ?>
  </div>
<?php } ?>


  <?php if ($sf_user->isAuthenticated()) { ?>

  <?php } ?>
  <div style="clear: both; height: 20px;"></div>
</div>
<div id="footer">
  <hr>
  <p>Copyright &copy; 2008 - <?php echo date('Y') ?> | ZeusCMS is ontwikkeld door <a href="http://mizar-it.nl">Mizar IT</a> | Alle rechten zijn voorbehouden</p>
</div>
<?php
$favs = zeusFavorites::get();
if (count($favs) > 0) { 
  $c = 0;
  $html = '';
  foreach ($favs as $fav) {
    list($name, $actionurl) = $fav;
    $c++;
    $checked = $c == 1 ? ' checked="checked"' : '';
    $html .= '<input'.$checked.' class="checkbox" type="radio" name="add-fav" id="add-fav-'.$c.'" value="'.$actionurl.'"> <label id="add-fav-label-'.$c.'" for="add-fav-'.$c.'">'.$name.'</label><br>';
  }
  ?>
<script type="text/javascript">
$('favorites-add-btn').removeClassName('disabled');
$('favorites-add-btn-icon').src = '/zeusCore/img/icons/famfamfam/add.png';
$('favorites-add-inner').innerHTML = '<?php echo $html; ?>';
var favs = <?php echo $c; ?>;
</script>
<?php
}

