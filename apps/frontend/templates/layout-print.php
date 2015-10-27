<!DOCTYPE html>
<html>
<head>
  <?php
  use_helper('sfCombine');
  include_http_metas();
  include_metas() ?>
  <link rel="icon" href="//<?php echo $_SERVER['SERVER_NAME']; ?>/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="//<?php echo $_SERVER['SERVER_NAME']; ?>/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" type="text/css" media="print" href="/css/iwerkbon-print.css">
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
  <div id="header">
    <div id="logo">
      <a href="<?php echo url_for('@homepage'); ?>"><img src="/img/logo-iwerkbon.png" alt="iWerkbon"></a>
    </div>
  </div>
  <div id="content">
    <div id="content-inner" class="wide">
      <?php echo $sf_content; ?>
    </div>
  </div>
</div>
</body>
</html>