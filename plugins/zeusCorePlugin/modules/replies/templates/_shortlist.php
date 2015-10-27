<h2><?php echo __('Reacties'); ?></h2>
<div class="content-inner-inner">
  <div class="content-inner-inner-right"></div>
  <div class="content-inner-content">
<?php
$df = new sfDateFormat('nl_NL');
$onoff = false;
if ($replies->getCount() > 0) { ?>
  <div>
    <ul id="reply-shortlist">
<?php foreach ($replies->getReplies() as $reply) {
  $class = $onoff ? 'row-a' : 'row-b';
  $onoff = !$onoff;
  ?>
      <li class="<?php echo $class ?>">
        <ul class="tools">
          <li class="date"><?php echo __('Geplaatst door %1% op %2%', array('%1%' => '<a href="mailto:ricardo.matters@mizar-it.nl">Ricardo</a>', '%2%' => $df->format(strtotime($object->getDate()), 'dd MMMM'))); ?></li>
          <li class="spam"><a href=""><?php echo __('Dit is niet OK'); ?></a></li>
<?php if (zeusVisitor::getInstance()->isLoggedIn() && zeusVisitor::getInstance()->getVisitor()->getId() == $reply->getVisitorId()) { ?>
          <li class="comment-edit"><a href=""><?php echo __('Bericht aanpassen'); ?></a></li>
<?php } ?>
          <li class="comments"><a href=""><?php echo __('Quote deze opmerking'); ?></a></li>
        </ul>
        <div class="reply-contents"><?php echo zeusReplies::format($reply->getMessage()); ?></div></li>
<?php } ?>
    </ul>
    </div>
<?php
}
else { ?>
    <p><strong><?php echo __('Er zijn nog geen reacties'); ?>.</strong></p>
<?php } ?>
  </div>
  <div class="content-inner-inner-footer">
    <div class="content-inner-inner-footer-right"></div>
  </div>
</div>
<h2><?php echo __('Reageren'); ?></h2>
<div class="content-inner-inner">
  <div class="content-inner-inner-right"></div>
  <div class="content-inner-content">

    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form">
      <fieldset>
        <legend>Comments</legend>
        <input type="hidden" name="formname" value="reply">
        <p><?php echo __('U kunt hieronder op het artikel reageren. Ongewenste en kwetsende reacties zullen worden verwijderd en kunnen een tijdelijke danwel permanente ban tot gevolg hebben.'); ?></p>
        
        <?php if (count($errors) > 0) { 
          echo '<ul class="form-errors">';
          foreach ($errors as $field => $error) {
            echo '<li>'.$error.'</li>';
          }
          echo '</ul>';
        } ?>
        <?php
        if (zeusVisitor::getInstance($_SERVER['REQUEST_URI'])->isLoggedIn()) { ?>
            <p><?php echo __('U bent ingelogd als'); ?> <strong><?php echo zeusVisitor::getInstance()->getName(); ?></strong>. <?php echo __('Wilt u zich'); ?> <a href="<?php echo url_for('community/logoff') ?>"><?php echo __('Afmelden'); ?></a>?</p>
        <?php }
        else { ?>
        
        
        <div class="form-row">
          <div class="form-label"><label for="reply-name"><?php echo __('Naam'); ?></label></div>
          <input <?php if (isset($errors['reply-name'])) echo 'class="error" '; ?>type="text" name="reply-name" id="reply-name" value="<?php echo $sf_params->get('reply-name'); ?>">
        </div>
       
        <div class="form-row form-row-wide">
          <div class="form-label"><label for="reply-email"><?php echo __('E-mail adres'); ?></label></div>
          <input <?php if (isset($errors['reply-email'])) echo 'class="error" '; ?> type="text" name="reply-email" id="reply-email" value="<?php echo $sf_params->get('reply-email'); ?>"> ( <?php echo __('wordt niet getoond bij de reactie'); ?> )
        </div>
      
        <div style="clear:both;"></div>
        <p><?php echo __('Neem de beveiligingscode over die in het plaatje wordt getoond. Deze is nodig om te controleren of u een echt persoon bent. Dit voorkomt vervuiling van de website door bijvoorbeeld spambots'); ?>.</p>
        <div class="form-row form-row-wide">
          <div class="form-label"><label for="captcha-reply"><?php echo __('Beveiligingscode'); ?></label></div>
          <?php 
          $captcha = zeusCaptcha::getInstance('reply'); 
          if (isset($errors['captcha'])) $captcha = str_replace('<input', '<input class="error"', $captcha);
          echo $captcha;
          ?>
        </div>
        
        <?php } ?>
        <div class="form-text">
          <textarea cols="30" rows="30" name="reply" id="reply"><?php echo $sf_params->get('reply'); ?></textarea>
          <div style="clear:both;"></div>
        </div>
        <div class="form-button">
          <button type="submit"><?php echo __('Reactie plaatsen'); ?></button>
        </div>
      </fieldset>
    </form>
    
    
  </div>
  <div class="content-inner-inner-footer">
    <div class="content-inner-inner-footer-right"></div>
  </div>
</div>