<?php if ($page) { ?>
<h1><?php echo $page->getTitle(); ?></h1>
<?php
}
ob_start();
?>
<div id="home-form">
<form action="<?php echo url_for('form/index?f='.$sf_params->get('f')) ?>" method="post" enctype="multipart/form-data" class="form">
  <fieldset>
    <legend>Formulier</legend>
    <?php
if ($form->hasErrors()) { 
?>
      <ul class="form-errors">
<?php 
  foreach ($form as $key => $field) {
    echo __($field->renderError());
  } 
?>
      </ul>
<?php 
} 
echo $form->renderHiddenFields();

preg_match_all('/<div class="form-label">(.+?)checkbox(.+?)<\/div>/si', $form, $ar);
foreach ($ar[0] as $match) {
  $rep = str_replace('<div class="form-label">', '', $match);
  $rep = str_replace('</label></div>', '</label> ', $rep);
  
  preg_match('/(<input.+?\/>)/', $rep, $ar2);
  
  $rep = $ar2[0].str_replace($ar2[0], '', $rep);
  $form = str_replace($match, $rep, $form);
}

echo $form;
?>
    <div class="form-buttons">
      <button type="submit" class="c2a"><?php echo __('Verzenden'); ?><span class="fa fa-caret-right"></span></button>
    </div>
  </fieldset>
</form>
</div>
<?php

if (isset($form_config['helper'])) {
  use_helper($form_config['helper']['helper']);
  echo $form_config['helper']['method']();
}

$content = ob_get_clean();

if ($page) {
  $ret = str_replace('<p>%%%FORM%%%</p>', $content, $page->getContent());
  $ret = str_replace('%%%FORM%%%', $content, $ret);
  echo $ret;
}
else {
  echo $content;
}