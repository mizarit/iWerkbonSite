<?php
$indent = isset($indent) ? $indent : 0;
$padding = str_repeat('  ', $indent);

$c = 0;
$total = count($root->getChildren());

if ($total > 0) { // hasChildren fails $root->hasChildren()) { ?>
<?php echo $padding ?><ul<?php if(isset($id)) echo ' id="'.$id.'"';?> <?php if(isset($class)) echo ' class="'.$class.'"';?>>
<?php
  $env = sfConfig::get('sf_environment') == 'dev' ? '/frontend_dev.php' : '';

  foreach ($root->getChildren() as $child) { 
    $c++;
    $url = '';
    switch($child->getType()) {
      case 'intern':
        $url = route_for($child->getValue());
        break;
      case 'extern':
        $url = str_replace('nl_NL',$sf_user->getCulture(), $child->getValue());
        break;
      case 'email': 
        $url = 'mailto:'.$child->getValue();
        break;
        
      default:
        $url = '#';
        break;
    }
    
    $forced = false;
    if ($sf_params->has('force_active_url')) {
      $forced = $sf_params->get('force_active_url');
    }
    
    $li_classes = array();
    if (in_array($url, $active_urls) || $url === $forced || $url == $_SERVER['REQUEST_URI'] || $url == $env.'/home' && trim($_SERVER['REQUEST_URI'],'/') == trim($env, '/')) {
      $li_classes[] = 'active-item'; 
    }
    if ($c == 1) {
      $li_classes[] = 'first-item';
    }
    
    if ($c == $total) {
      $li_classes[] = 'last-item';
    }
    
    $li_class = count($li_classes) > 0 ? ' class="'.implode(' ', $li_classes).'"' : '';
    
    ?>
<?php echo $padding ?>  <li<?php echo $li_class ?>><a href="<?php echo $url ?>"><?php echo $child->getTitle() ?></a><?php
    if (count($child->getChildren()) > 0) {
      echo "\n";
      include_component('menu', 'default', array('root' => $child, 'indent' => $indent + 1, 'active_urls' => $active_urls));
    }
    ?></li>
<?php
  }
?>
<?php echo $padding ?></ul>
<?php
}
?>