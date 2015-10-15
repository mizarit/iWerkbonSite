<ul>
<?php
foreach ($cultures as $culture) {
  $class = '';
  $image = '/img/lang-'.strtolower(substr($culture,3,2));
  
  if ($culture == $current_culture) {
    $class = ' class="active"';
  }
  else {
    //$image .= '-void';
  }
  $image .= '.png';
  
  $title = zeusI18N::getLanguageName($culture, $current_culture);
  ?>
  <li title="<?php echo $title ?>" <?php echo $class ?>><a href="<?php echo url_for('i18n/switch?culture='.$culture); ?>"><img src="<?php echo $image ?>" alt="<?php echo $title ?>"></a></li>
<?php } ?>
</ul>