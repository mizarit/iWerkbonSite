<?php
$app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
$cfg = $app['all']['facebook'];
$fb = zeusFacebook::getInstance($cfg['appid'], $cfg['appsecret']);
$fb_login = $fb->showLogin();
if ($fb->isLoggedIn()) {
  $client = new Facebook(array('appId' => $cfg['appid'], 'secret' => $cfg['appsecret']));

  if ($sf_params->has('ids')) {
    $ids = explode(',', trim($sf_params->get('ids'), ','));
   
    foreach ($ids as $id) {
      $client->api('/'.$id.'/feed', 'post', array(
        'message' => 'Ik heb de last-minute '.$object->getTitle(). ' met een aanzienlijke korting gereserveerd!',
        'link' => 'http://'.$_SERVER['HTTP_HOST'].route_for($object),
        'name' => 'PlekjeVrij',
        'caption' => $object->getTitle(),
        'description' => 'Met PlekjeVrij.nl kun je met aanzienlijke korting direct online een afspraak maken voor een aantrekkelijke lastminute deal! Onze deals bevatten; wellness en verzorging, kapsalons, restaurants, dagje uit en nog veel meer.',
      ));
    }
    
    echo 'OK';
    exit;
  }
}
?>
<div id="facebook-form">

  <h3>Reserveer: '<?php echo $object->getTitle(); ?>'</h3>
                
                <?php use_helper('ZeusEdit'); ?>
                <?php echo zeus_edit_textpartial(null, '', '', array('key' => 'facebook boeking bedankt')); ?>
                
                <p><strong>Je reservering is gemaakt.</strong></p>
                
                <p>Je ontvangt per e-mail een bevestiging van de reservering en de betaling.</p>
                
                <button class="button-1-a" onclick="window.location.href='<?php echo url_for('@facebook_app'); ?>';"><span>Bekijk meer last-minutes</span></button>
                
                <br><br><br><br>

                <h4>Deel dit met je vrienden</h4>
                <p>Deel dit met je vrienden, zodat ook zij gebruik kunnen maken van de grote kortingen op PlekjeVrij.nl</p>
                <?php 
                $title = $object->getTitle();
                $url = 'http://plekjevrij.nl'.route_for($object);
                ?>
                <ul class="social-icons">
                  <li><img src="/img/soc-twitter.png" title="<?php echo __('Deel dit op Twitter'); ?>" alt="<?php echo __('Deel dit op Twitter'); ?>" style="cursor:pointer;" onclick="window.open('http://twitter.com/intent/tweet?text=<?php echo $title; ?>&amp;url=<?php echo $url; ?>&amp;via=PlekjeVrij', 'share', 'menubar=0,resizable=1,status=0,width=450,height=400');"></li>
                  <li><img src="/img/soc-facebook.png" title="<?php echo __('Deel dit op Facebook'); ?>" alt="<?php echo __('Deel dit op Facebook'); ?>" style="cursor:pointer;" onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo $url; ?>', 'share', 'menubar=0,resizable=1,status=0,width=750,height=330');"></li>
                </ul><br><br><br><br>

                
<!-- Google Code for Reservering Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1022943205;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "HV5hCLv60QMQ5b_j5wM";
var google_conversion_value = 6;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1022943205/?value=6&amp;label=HV5hCLv60QMQ5b_j5wM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<h4>Welke Facebook vrienden neem je mee?</h4>
<p>Hieronder kun je de Facebook vrienden selecteren waarmee je naar deze last-minute gaat.</p>
<p><button class="button-1-a" type="button" onclick="sendRequestViaMultiFriendSelector();"><span>Vrienden selecteren</span></button></p>
<div id="message"></div>

<script type="text/javascript">
var send_invitation_url='http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>';
  
FB.init({
  appId  : '<?php echo $cfg['appid']; ?>',
  frictionlessRequests: true
});
      
function sendRequestViaMultiFriendSelector() {
  FB.ui({
    method: 'apprequests',
    app_id: <?php echo $cfg['appid']; ?>, 
    message: "Reserveer net als mij leuke last-minutes met hoge korting!"
  },send_wall_invitation);
}

function send_wall_invitation(response) {
  var ids = '';
  response.to.each(function(s,i){
    ids += ','+s;
  });
  
  new Ajax.Request(send_invitation_url, 
  { parameters: { ids: ids },
    onSuccess: function(response) {
      if (response.responseText == 'OK') {
        $('message').innerHTML = '<p><strong>Je vrienden zijn uitgenodigd!</strong></p>';
      }
    }
  });
}
</script>
</div>
<div id="facebook-form-footer"></div>