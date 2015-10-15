<ul id="crumblepath">
  <li><strong>Je bent nu hier:</strong></li>
  <li><a href="<?php echo url_for('@homepage'); ?>">home</a></li>
  <?php 
  $c = 0;
  foreach ($items as $url => $title) { 
    $c++;
    $cls = $c == count($items) ? " class='active-item'" : '';
    ?>
  <li<?php echo $cls; ?>>/&nbsp;&nbsp;<a href="<?php echo route_for($url); ?>"><?php echo $title; ?></a></li>
  <?php } ?>
</ul>