<form action="<?php echo url_for('form/index?f=contact'); ?>" method="post" enctype="multipart/form-data" class="form">
<?php

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
</form>