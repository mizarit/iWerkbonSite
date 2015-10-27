<?php
$indent = isset($indent) ? $indent : 0;
$padding = str_repeat('  ', $indent);

if ($root && count($root->getChildren()) > 0) { // hasChildren fails $root->hasChildren()) { ?>
<?php echo $padding ?><ul<?php if(isset($id)) echo ' id="'.$id.'"';?>>
<?php
  $env = sfConfig::get('sf_environment') == 'dev' ? '/frontend_dev.php' : '';

  $count = 0;
  $objects = $root->getChildren();
  $total = count($objects);
  foreach ($objects as $child) { 
    $count++;
    $url = '';
    switch($child->getType()) {
      case 'intern':
        $url = route_for($child->getValue());
        break;
      case 'extern':
        $url = $child->getValue();
        break;
      case 'email': 
        $url = 'mailto:'.$child->getValue();
        break;
        
      default:
        $url = '#';
        break;
    }
    
    $forced = '';
    if ($sf_params->has('force_active_url')) {
      $forced = $sf_params->get('force_active_url');
    }
    
    $li_classes = array();
    if ($count == 1) {
      $li_classes[] = 'first-item';
    }
    
    if ($count == $total) {
      $li_classes[] = 'last-item';
    }
    
    if (($url == $forced || $url == $_SERVER['REQUEST_URI'] || $url == $env.'/home' && $_SERVER['REQUEST_URI'].'/' == url_for('@homepage'))) {
      $li_classes[] = 'active-item';
    }
    
    $li_class =  count($li_classes) > 0 ? ' class="'. implode(' ', $li_classes).'"' : '';
    
    ?>
<?php echo $padding ?>  <li<?php echo $li_class ?>><a href="<?php echo $url ?>"><?php echo $child->getTitle() ?></a><?php
    if (count($child->getChildren()) > 0) {
      echo "\n";
      include_component('menu', 'default', array('root' => $child, 'indent' => $indent + 1));
    }
    ?></li>
<?php
  }
?>
<?php echo $padding ?></ul>
<?php
}
?>