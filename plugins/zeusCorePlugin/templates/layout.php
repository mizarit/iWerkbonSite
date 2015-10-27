<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
<?php 
include_http_metas();
include_metas();
$title = $sf_response->getTitle();
$sf_response->setTitle('Zeus Content Management - '.$title);
include_title();
$sf_response->setTitle($title);
include_stylesheets();
include_javascripts() 
?>
</head>
<script type="text/javascript">
<?php 
$controller = sfConfig::get('sf_app') . (sfConfig::get('sf_environment') == 'dev' ? '_dev' : ''). '.php';
echo "var zeusController = '/{$controller}';\n";
?>
</script>
<div id="logo">
  <h1><a href="<?php echo url_for('@homepage') ?>">Zeus<span>CMS</span></a></h1>
<?php if ($sf_user->isAuthenticated()) { ?>
  <p id="top-icons"><a href="<?php echo url_for('securityadmin/logoff') ?>"><img src="/zeusCore/img/icons/icon-logoff.gif" alt="Afmelden">afmelden</a></p>
 <?php } ?>
</div>
<div id="nav">
  <ul>
<?php 
    



if ($sf_user->isAuthenticated()) {
	$modules = sfConfig::get('app_backendmodules_list');
}
else {
	$modules = array(
    'dashboardadmin' => 'Dashboard'
   );
}
 
var_dump($modules);
exit;
foreach ($modules as $module => $label) {
  if (is_array($label)) {
    print_r($label);
    exit;
    continue;
  }
  $active = $sf_params->get('module') == $module ? ' class="active"' : '';
?>
    <li<?php echo $active?>><a href="<?php echo url_for($module.'/index') ?>"><?php echo $label ?></a></li>
<?php 
} 
?>
  </ul>
  <hr>
</div>
<?php echo zeusRibbon::get() ?>
<div id="container">
  <div id="container-inner">
  <?php echo $sf_content ?>
  </div>
  <?php if ($sf_user->isAuthenticated()) { ?>

  <?php } ?>
  <div style="clear: both; height: 20px;"></div>
</div>
<div id="footer">
  <hr>
  <p>Copyright &copy; 2008 - <?php echo date('Y') ?> <a href="http://mizar-it.nl">Mizar IT</a>. Alle rechten zijn voorbehouden.</p>
</div>

