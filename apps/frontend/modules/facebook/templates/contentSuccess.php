<?php
ob_start();
sfContext::getInstance()->getResponse()->addMeta('title', $object->getTitle(), true);
sfContext::getInstance()->getResponse()->setTitle($object->getTitle());
?>
<div style="margin: 0 auto;width:380px;border: #cecece 1px solid;border-radius:5px;padding:20px 20px 0 20px;">
<form action="#" method="POST">
<?php if (count($errors) > 0) { ?>
  <ul class="form-errors" style="width: 362px;">
  <?php foreach ($errors as $key => $error) { ?>
    <li><?php echo $error; ?></li>
  <?php } ?>
  </ul>
 <?php } ?>
 
 <?php if (isset($message)) { ?>
 <ul class="form-messages">
 <li><?php echo $message; ?></li>
 </ul>
 <?php } else { ?>
 <div class="form-row">
    <div class="form-label"><label for="name">Naam</label></div>
    <input type="text" name="name" id="name" value="<?php echo $name; ?>">
  </div>
  <div class="form-row">
    <div class="form-label"><label for="email">E-mailadres</label></div>
    <input type="text" name="email" id="email" value="<?php echo $email; ?>">
  </div>
  <div class="form-row">
    <div class="form-label"><label for="zipcode">Postcode</label></div>
    <input type="text" name="zipcode" id="zipcode" value="<?php echo $zipcode; ?>" style="width:80px;">
  </div>
  <div class="form-row">
    <input type="checkbox" class="checkbox" name="agree" id="agree">
    <label for="agree">Ik ga akkoord met de actievoorwaarden en wil op de hoogte gehouden worden van toekomstige acties.</label>
  </div>
  <div class="form-button" style="text-align:right;">
    <button class="button-1"><div>Ik doe mee!</div></button>
  </div>
<?php } ?>
</form>
</div>
<?php
$form = ob_get_clean();
ob_start();
?>
<div id="social-box">
  <div class="fb-like" data-href="http://plekjevrij.nl<?php echo $_SERVER['REQUEST_URI']; ?>" data-app-id="<?php echo $app['all']['facebook']['appid']; ?>" data-send="false" data-width="120" data-show-faces="true" data-action="like"></div>
</div>
<?php
$social = ob_get_clean();
?>
<div id="facebook-form">
  <button class="button-1-b" onclick="window.location.href='<?php echo url_for('@facebook_app'); ?>';"><span>Vorige pagina</span></button>
  <h3><?php echo $object->getTitle(); ?></h3>
  <?php echo str_replace('%%%SOCIAL%%%', $social, str_replace('%%%FORM%%%', $form, $object->getContent())); ?>
  </div>
<div id="facebook-form-footer"></div>
