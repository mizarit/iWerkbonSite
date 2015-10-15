<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="title" content="Welkom bij Mizar IT!">
  <title>Welkom bij Mizar IT!</title>
  <link rel="stylesheet" type="text/css" media="screen" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/css/initial.css">
  <link rel="stylesheet" type="text/css" media="print" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/css/initial.css">
  <link rel="stylesheet" type="text/css" media="screen" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/css/mizar-it-screen.css">
  <link rel="stylesheet" type="text/css" media="print" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/css/mizar-it-screen.css">
</head>
<style type="text/css">
@media print {
  #explanation { 
    display:none;
  }
}
</style>
<div id="container">
  <div id="explanation" style="border:#cecece 2px dotted;margin: 20px;padding: 10px;">
    <h1><?php echo ucfirst($sf_request->getParameter('documenttype')); ?> afdrukken</h1>
    <p>Druk deze pagina af met de volgende printer instellingen ( in Firefox ):</p>
    <p>Ga naar Bestand -> Pagina instellen en zorg dat instellingen gelijk zijn aan:</p>
    <p><img src="/img/printerhelp-1.png" alt=""></p>
    <p>Ga naar Marges en kop/voetteksten en zorg dat instellingen gelijk zijn aan:</p>
    <p><img src="/img/printerhelp-2.png" alt=""></p>
  </ul>

</div>
  <div id="header">
    <div style="width:950px;position: relative;margin: 0 0 0 37px;">
      <a href="/" class="logo"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/logo-mizar-it.png" alt="Mizar IT"></a>
      <p style="width: 300px;position: absolute;left: 390px;top:0;color:#fff;text-align: right;"><strong>Mizar IT</strong><br>
      www.mizar-it.nl | info@mizar-it.nl<br>
      KvK 27349424 | BTW NL175610058B01 | ING 6456878
      </p>
      <h1 style="position:absolute;left: 600px;top:60px;color:#fff;"><?php echo strtoupper($sf_request->getParameter('documenttype')); ?></h1>
    </div>
  </div>

  <div style="background: url(/img/header-inner-bkg.gif) repeat-x;padding-top: 20px;">
    <div style="width:950px;margin: 0 0 0 37px;">
      <div class="col-1" style="border:0;width: 680px;">
<?php 
$pages = explode('%%%PAGEBREAK%%%', $sf_content);
$not_first = false;
foreach ($pages as $page) {
  if ($not_first) {
    ?>
        </div>
    </div>
  </div>
  <div style="clear:both;page-break-after:always"></div>
  <div class="header">
    <div style="width:950px;position: relative;margin: 0 0 0 37px;">
      <a href="/" class="logo"><img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/img/logo-mizar-it.png" alt="Mizar IT"></a>
    </div>
  </div>
    <div style="background: url(/img/header-inner-bkg.gif) repeat-x;padding-top: 20px;">
    <div style="width:950px;margin: 0 0 0 37px;">
      <div class="col-1" style="border:0;width: 680px;">
  <?php
  }
  $not_first = true;
  ?>
  
  <?
  echo $page;
}
?>

      </div>
    </div>
  </div>
</div>