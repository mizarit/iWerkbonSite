<?php
/*
echo '<pre>';
$w = stream_get_wrappers();
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
echo 'wrappers: ', var_dump($w);

$url = 'https://idealtest.secure-ing.com:443/ideal/iDeal';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_REFERER, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
    
exit;
*/
$site_maintenance = false;

if ($site_maintenance && $_SERVER['REMOTE_ADDR'] != '83.86.233.190') { ?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="plekjevrij.nl, last-minutes, Beauty &amp; Verzorging, Eten &amp; Drinken, Gezondheid, Leuke dingen doen, Ontspanning, Overnachting &amp; Vakantie, Sport, Voorstellingen &amp; Evenementen, Alkmaar, Amersfoort, Amsterdam, Den Haag, Groningen, Leeuwarden, Rotterdam, Utrecht, korting, afspraak maken">
<meta name="description" content="Bij PlekjeVrij.nl vind je aantrekkelijke last-minute deals die je direct online kunt reserveren.">
<title>Last-minute talloze diensten reserveren</title>
<link rel="stylesheet" type="text/css" media="screen" href="/css/normalize.css">
<link rel="stylesheet" type="text/css" media="screen" href="/css/plekjevrij-screen.css">

<body style="background: #fff;">
<div style="margin: 0 auto; width: 400px;border:#bfeffb 2px solid;padding:5px 5px 20px 5px;position: relative;margin-top:140px;">
<div class="heading" style="margin-bottom: 20px;">
  <h3>Website offline</h3>
</div>
<p>Helaas is de website momenteel in verband met werkzaamheden niet bereikbaar. Probeer het later nog eens.</p>
<img src="/img/logo-plekjevrij-2.png" alt="PlekjeVrij" style="position: absolute;top:-85px;right:-25px;">
</div>
</body>
<?php
exit;
} else {
//echo hash('sha512', 'id7026991d35a098f6bcd471346689904');
//exit;
//if (!in_array($_SERVER['REMOTE_ADDR'], array('83.86.100.17'))) exit;

set_time_limit(0);
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
}