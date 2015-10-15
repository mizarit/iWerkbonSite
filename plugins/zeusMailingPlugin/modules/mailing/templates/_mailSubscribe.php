<?php
$color1 = '2f2c3d';
$color2 = '097ac0';
$logo = 'logo-mailing.png'; // located in /web/img/mailing/
?><style type="text/css">
#mailing {
  width: 700px;
}

#mailing a img {
  border: none;
}

#mailing * {
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

#mailing a {
  color: #<?php echo $color2; ?>;
}

#mailing p {
  margin-bottom: 8px;
  line-height: 20px;
  text-align: justify;
}

#mailing h1, 
#mailing h2, 
#mailing h3, 
#mailing h4, 
#mailing h5, 
#mailing h6 {
  border-bottom: #cecece 1px dotted;
  color: #<?php echo $color2; ?>;
  font-weight: normal;
  letter-spacing: -1px;
  position: relative;
  text-transform: lowercase;
}

#mailing h1 {
  font-size: 18px;
  text-transform: none;
}

#mailing h2 {
  border-bottom: #cecece 1px dotted;
  font-size: 16px;
  margin: 20px 0 5px 0;
}

#mailing h3 {
  border: none;
  font-size: 12px;
  font-weight: bold;
  margin: 20px 0 5px 0;
  padding-bottom: 3px;
}

#mailing h3 span {
  color: #333;
  /*font-weight: normal;*/
  font-size: 14px;
}
</style>
<?php
$host = 'http://'.str_replace('cms.', '', $_SERVER['HTTP_HOST']);
$image_path = $host.'/img/mailing/';
$url = $host.url_for('mailing/confirm?m=subscribe&l='.$mailinglist_id.'&e='.str_replace('=', '__', base64_encode($email)).'&h='.substr(md5($email.$mailinglist_id),6,6));
?>
<div id="mailing">
<table cellpadding="0" cellspacing="0" width="700">
  <tr>
    <td style="width:530px;"></td>
    <td style="width:170px;"><img border="0" src="<?php echo $image_path; ?><?php echo $logo; ?>" alt=""></td>
  </tr>
</table>
<h1><?php echo __('Aanmelden nieuwsbrief'); ?></h1>
<p><?php echo __('Klik op onderstaande link om uw aanmelding op onze nieuwsbrief te bevestigen'); ?>:</p>
<p><a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
<div style="height:25px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" style="width: 700px;padding:0;border-collapse:collapse;">
  <tr><td style="background:#<?php echo $color2; ?>;">
</table>
</div>