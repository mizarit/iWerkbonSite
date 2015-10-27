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
<?php echo $sf_content ?>