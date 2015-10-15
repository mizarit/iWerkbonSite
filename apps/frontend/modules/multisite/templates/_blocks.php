<?php 
$active = '';
foreach ($services as $key => $service) { 
  if (isset($service['active'])) $active = $key;
}
?>   
    <ul id="nav-blocks" class="active-<?php echo $active; ?>">
      <li class="first-item"><p><?php echo __('Kies uw product'); ?></p></li>
<?php 
$c = 0;
foreach ($services as $key => $service) { 
  $c++;
  $classes = array();
  if (isset($service['active'])) $classes[] = 'active-item';
  if ($c == count($services)) $classes[] = 'last-item';
  $class = count($classes) > 0 ? ' class="'.implode(' ', $classes).'"' : '';
  ?>
      <li onclick="window.location.href='<?php echo $service['url'].'/'.$sf_user->getCulture(); ?>/welkom';"<?php echo $class; ?> id="nav-<?php echo $key; ?>-<?php echo $language; ?>"><a href="<?php echo $service['url']; ?>"><?php echo $service['title']; ?></a></li>
<? } ?>
      <li class="bottom-item">&nbsp;</li>
    </ul>