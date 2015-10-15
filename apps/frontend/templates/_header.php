<!DOCTYPE html>
<html>
<head>
<?php
use_helper('sfCombine');
include_http_metas();
include_metas() ?>
<link rel="icon" href="//<?php echo $_SERVER['SERVER_NAME']; ?>/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="//<?php echo $_SERVER['SERVER_NAME']; ?>/favicon.ico" type="image/x-icon">
<?php
include_title();
include_stylesheets();
include_component('core', 'frontend');
include_javascripts();
?>
<?php if (has_slot('opengraph')) include_slot('opengraph'); ?>
<link rel="alternate" title="RSS Feed" type="application/rss+xml" href="<?php echo url_for('rss/feed?proxy=news'); ?>">
<script src="//use.typekit.net/dzg0uxe.js"></script>
<script>
try{Typekit.load();}catch(e){}
var trackOutboundLink = function(url) {
   ga('send', 'event', 'outbound', 'click', url, {'hitCallback':
     function () {
     document.location = url;
     }
   });
}
</script>
</head>
<body id="body">
<div id="container">
  <div id="modal">
    <div id="overlay"></div>
    <div id="modal-inner">
      <h2 id="modal-title"><i id="dialog-title">Dialog title</i><span class="fa fa-close" onclick="$('modal').removeClassName('active');"></span></h2>
      <div id="dialog-content">
        test
        </div>
    </div>
  </div>
  <div id="modal-micro">
    <div id="overlay-micro"></div>
    <div id="modal-inner-micro">
      <h2 id="modal-title-micro"><i id="dialog-title-micro">Dialog title</i><span class="fa fa-close" onclick="$('modal-micro').removeClassName('active');"></span></h2>
      <div id="dialog-content-micro">
        test
      </div>
    </div>
  </div>
  <div id="header">
    <div id="logo">
      <a href="<?php echo url_for('@homepage'); ?>"><img src="/img/logo-iwerkbon.png" alt="iWerkbon"></a>
    </div>
    <div id="nav-container">
      <div id="nav">
        <?php include_component('admin', 'nav'); ?>
      </div>
      <!--<div id="crumblepath">
        <?php include_component('admin', 'crumblepath'); ?>
      </div>-->
    </div>
    <div id="user-info">
      <?php include_component('admin', 'user'); ?>
    </div>
  </div>
<?php

/*
//$address = urlencode('Rosa Manusstraat 18 2331MC Leiden');
$address = urlencode('Doeszijde 4 2351 EW Leiderdorp');

$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false");
$json = json_decode($json);
$lat = $json->results[0]->geometry->location->lat;
$long  = $json->results[0]->geometry->location->lng;
*/
?>
