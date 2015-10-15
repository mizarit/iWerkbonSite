<?php use_helper('ZeusEdit'); ?>
<div id="filters" class="newsletter">
<!--
<h2>Nieuwsbrief</h2>
  <div class="section">
    <div class="tinymce"><?php echo zeus_edit_textpartial(null, '', '', array('key' => 'mailing uitleg links nieuwsbrief')); ?></div>
    <?php include_component('mailing', 'subscribe', array('mailinglist' => 1)); ?>
  </div>
-->
  <h2>Aanbiedingen</h2>
  <div class="section">
    <div class="tinymce"><?php echo zeus_edit_textpartial(null, '', '', array('key' => 'mailing uitleg links aanbiedingen')); ?></div>
    
    <form action="#" method="post">
                				<fieldset>
                				
                				<div class="form-row form-checkbox" style="border: none;">
                				  <input type="checkbox"<?php if ($sf_params->has('accept-tagletter') || $sf_request->getMethod() == 'GET') echo ' checked="checked"'; ?> class="checkbox" name="accept-tagletter" id="interval-chk" onchange="if(this.checked){$('interval-section-1').style.display='block';}else{$('interval-section-1').style.display='none';}"> <label for="interval-chk">Attendeer mij op interessante last-minutes.</label>
                				</div>
                				
                				<div id="interval-section-1"<?php if (!$sf_params->has('accept-tagletter') && $sf_request->getMethod() != 'GET') echo ' style="display: none;"'; ?>>
                  				<div class="form-row" style="border: none;">
                  				  <?php 
      $c = new Criteria; 
      $c->addAscendingOrderByColumn(CategoryI18NPeer::TITLE);
      $categories = CategoryPeer::doSelectWithI18N($c, 'nl_NL');
      $categoryIds = array();
      
      foreach ($categories as $category) { 
        $check = in_array($category->getId(), $categoryIds) || $sf_request->getMethod() == 'GET'  ? ' checked="checked"' : '';
        ?>
      <div style="margin-top: 5px;margin-left:15px;"><input type="checkbox" <?php echo $check; ?> class="checkbox" value="<?php echo $category->getId(); ?>" id="category-<?php echo $category->getId(); ?>" name="category[]"> <label for="category-<?php echo $category->getId(); ?>"><?php echo $category->getTitle(); ?></label></div>
      <?php } ?>
                  				</div>
                  				
                  				<div class="form-row" style="margin-top: 10px;border: none;">
                  				  <div class="form-label" style="width:90px;"><label for="newsletter-frequency">Hoe frequent?</label></div>
                  				  <?php echo select_tag('newsletter-frequency', options_for_select(array(
                  				    1 => 'dagelijks',
                  				    7 => 'wekelijks',
                  				   /* 14 => '2-wekelijks',
                  				    30 => 'maandelijks',
                  				    91 => 'ieder kwartaal',
                  				    182 => 'ieder half jaar',
                  				    365 => 'jaarlijks'*/
                  				  
                  				  
                  				  ), $sf_params->get('newsletter-frequency')), array('style' => 'padding:4px;')); ?>
                  				</div>
                      				
                				</div>
<?php
$app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
$cfg = $app['all']['facebook'];
$fb = zeusFacebook::getInstance($cfg['appid'], $cfg['appsecret']);
$fb_login = $fb->showLogin();
$email = $zipcode = '';
if($sf_user->hasAttribute('consumer_id') && $sf_user->hasAttribute('consumer_account')) { 
  $consumer = ConsumerPeer::retrieveByPk($sf_user->getAttribute('consumer_id'));
  $email = $consumer->getEmail();
  $zipcode = $consumer->getZipcode();
}
else if ($fb->isLoggedIn()) {
  $email = $fb->getEmail();
}
if ($sf_params->has('email')) {
  $email = $sf_params->get('email');
  $zipcode = $sf_params->get('zipcode');
}

if ($email == '') {
  $email = 'je@e-mail.adres';
}
?>
              				  		
                				<div class="form-row" style="border: none;">
                				  <div class="form-label" style="width:70px;"><label for="email">E-mail adres *</label></div>
                				  <input type="text" name="email" id="email" value="<?php echo $email; ?>" style="width: 130px;padding:4px;" onclick="if(this.value=='je@e-mail.adres')this.value='';">
                				</div>
                				
                				<div class="form-row" style="border: none;">
                				  <div class="form-label" style="width:70px;"><label for="zipcode">Postcode *</label></div>
                				  <input type="text" maxlength="6" name="zipcode" id="zipcode" value="<?php echo $zipcode; ?>" style="width: 45px;padding:4px;"> <em style="font-size:9px;font-style:italic;">(1234AA)</em>
                				</div>
                				
                				<button class="button-1"><div>Aanmelden</div></button>
                				
                				</fieldset>
                				</form>
                				
  </div>  
          
</div>
<div id="content-inner" style="min-height:700px;">
              
              
  <div class="heading">
    <h1><?php echo str_replace('</p>', '', str_replace('<p>', '', zeus_edit_textpartial(null, '', '', array('key' => 'mailing uitleg titel')))); ?></h1>
  </div>
  
    <?php if ($message != '') { ?>
                <ul class="form-messages">
                  <li><?php echo $message; ?></li>
                </ul>
                <?php } ?>
                <?php if (count($errors) > 0) { ?>
                <ul class="form-errors">
                <?php foreach ($errors as $key => $error) { ?>
                  <li><?php echo $error; ?></li>
                <?php } ?>
                </ul>
                <?php } ?>
                
 
  <div class="tinymce"><?php echo zeus_edit_textpartial(null, '', '', array('key' => 'mailing uitleg')); ?></div>
</div>