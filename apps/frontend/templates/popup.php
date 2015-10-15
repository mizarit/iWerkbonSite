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
<style type="text/css">
button {
  background:transparent url(/zeusCore/img/ribbon/buttons/button-2-left.png) no-repeat scroll left 1px;
  cursor: pointer !important;
  color:#1b1b1b;
  font-family:Tahoma,Arial,Verdana,sans-serif;
  font-size:11px;
  border:0 none !important;
  height:25px;
  line-height:25px;
  margin:0 0 0 10px !important;
  padding:0 !important;
}

button div {
  background:transparent url(/zeusCore/img/ribbon/buttons/button-2-right.png) no-repeat scroll right top;
  cursor: pointer !important;
  height:25px;
  line-height:25px;
  margin:0 0 0 1px;
  padding:0 4px;
  color:#0a1725;
  font-family:Tahoma,Arial;
  font-size:11px;
}

button {
  background:transparent url(/zeusCore/img/ribbon/buttons/button-2-left.png) no-repeat scroll left 0px\9;
}

button div {
  margin: 0 0 0 4px\9;
}

button:hover {
  background:transparent url(/zeusCore/img/ribbon/buttons/button-2-hover-left.png) no-repeat scroll left 1px;
}

button:hover div {
  background:transparent url(/zeusCore/img/ribbon/buttons/button-2-hover-right.png) no-repeat scroll right top;
}

.image-preview {
  position: relative;
}

.image-preview img {
  border: #cecece 1px solid;
  padding: 2px;
}

.image-delete {
  background: url(/zeusCore/img/icons/famfamfam/cross.png) no-repeat;
  display: block;
  cursor: pointer;
  position: absolute;
  left: 4px;
  top: 4px;
  width: 16px;
  height: 16px;
  z-index: 2;
}

#files-container #ext-comp-1003 {
  height: 400px;
  overflow: scroll;
}


#annuleer-button:disabled {
    cursor: auto;
}
#annuleer-button {
    left: -10px;
    position: relative;
    top: -7px;
}
#files-browser-container {
  width: 648px;
}
</style>
<script type="text/javascript">
<?php 
$controller = sfConfig::get('sf_environment') == 'dev' ? 'frontend_dev.php' : 'index.php';
echo "var zeusController = '/{$controller}';\n";
?>
</script>
<?php echo $sf_content ?>