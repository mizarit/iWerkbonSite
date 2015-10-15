<?php
$color1 = '2f2c3d';
$color2 = '097ac0';
$logo = 'logo-mailing.png'; // located in /web/img/mailing/
?><style type="text/css">
#mailing * { 
  color: #<?php echo $color1; ?>;
  font-family: Verdana;
  font-size: 10px;
}

#mailing p {
  margin: 10px;
  line-height: 14px;
}

#mailing h2 {
  color: #<?php echo $color1; ?>;
  font-size: 14px;
  font-weight: bold;
  margin: 10px;
  padding: 0;
  *margin: 0;
}

#mailing td, #mailing tr, #mailing table { 
  padding: 0 !important;
  margin: 0 !important;
}

#mailing a img {
  border: none;
}
</style>
<?php
$host = 'http://'.str_replace('cms.', '', $_SERVER['HTTP_HOST']);
$image_path = $host.'/img/mailing/';
?>
<div id="mailing">
<table cellpadding="0" cellspacing="0" width="700">
  <tr>
    <td style="width:530px;"></td>
    <td style="width:170px;"><img border="0" src="<?php echo $image_path; ?><?php echo $logo; ?>" alt=""></td>
  </tr>
</table>
<h2><?php echo $mailing->getTitle(); ?></h2>
<?php echo htmlspecialchars_decode($mailing->getContent()); ?>
<div style="height:25px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" style="width: 700px;padding:0;border-collapse:collapse;">
  <tr><td style="background:#<?php echo $color2; ?>;">
  
  <p style="padding:0;margin:5px 0;color:#fff;text-align:center;">Kunt u de nieuwbrief niet goed lezen? klik dan <a style="color:#fff;" href="<?php echo $host; ?><?php if (sfConfig::get('sf_environment') == 'dev') echo '/frontend_dev.php'; ?>/mailing/viewonline?id=<?php echo $mailing->getId(); ?>">hier</a> voor de webversie. Wilt u de nieuwsbrief <a style="color:#fff;" href="<?php echo $host; ?>/mailing/unsubscribe?id=<?php echo $user->getId(); ?>&hash=<?php echo substr(md5($user->getEmail()), 0, 6); ?>">niet meer ontvangen</a>?</p>
</table>
</div>