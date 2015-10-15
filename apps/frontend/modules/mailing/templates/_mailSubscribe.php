<style type="text/css">
body {
  background: #fff;
}

p {
  width: 740px;
  margin: 0 10px 15px 10px;
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

td {
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
  padding: 0;
  margin: 0;
}

table {
  margin: 10px;
  border-collapse: collapse;
  padding: 0;
}

strong {
  color: #0b4a94;
  font-weight: bold;
}

h1 {
  font-family: "trebuchet ms",helvetica,sans-serif;
  width: 750px;
  margin: 10px 10px 15px 10px;
  border-bottom: #cecece 1px dotted;
  color: #0b4a94;
  font-weight: normal;
  letter-spacing: -1px;
  font-size: 24px;
}
</style>
<img src="http://plekjevrij.nl/img/header-mailing.png" alt="">


<?php
$host = 'http://'.str_replace('cms.', '', $_SERVER['HTTP_HOST']);
$image_path = $host.'/img/mailing/';
$url = $host.url_for('mailing/confirm').'?m=subscribe&l='.$mailinglist_id.'&e='.str_replace('=', '__', base64_encode($email)).'&h='.substr(md5($email.$mailinglist_id),6,6);
?>

<h1><?php echo __('Aanmelden nieuwsbrief'); ?></h1>
<p><?php echo __('Klik op onderstaande link om je aanmelding op onze nieuwsbrief te bevestigen'); ?>:</p>
<p><a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>


<p>Met vriendelijke groet,</p> 
<p>PlekjeVrij B.V.</p>
<p><br>Deze e-mail is automatisch verzonden op <?php echo date('d-m-Y'); ?>.</p>
<table width="760" style="background: #d1d51d;">
  <tr>
    <td colspan="2" style="height:120px;background:#f3f4c6;"></td>
  </tr>
  <tr>
    <td></td>
    <td style="height:120px;vertical-align:top;text-align:right;padding: 10px 10px 0 0;color:#0b4a94">
    Volg ons op 
    <a href="https://twitter.com/#!/plekjevrij"><img alt="" src="http://plekjevrij.nl/img/soc-twitter-small.png"></a>
 	  <a href="http://www.facebook.com/PlekjeVrij"><img alt="" src="http://plekjevrij.nl/img/soc-facebook-small.png"></a>
    </td>
  </tr>
</table>
