<?php if($sf_user->isAuthenticated()) { ?>
<ul>
  <?php
$urls = array(
    url_for('admin/planboard') => 'Planbord',
    url_for('admin/workorders') => 'Werkbonnen',
    url_for('admin/customers') => 'Klanten',
    url_for('admin/admin') => 'Administratie',
    url_for('admin/settings') => 'Instellingen'
  );
foreach ($urls as $url => $label) {
  $cls = '';
  if (strlen(str_replace('/frontend_dev.php/', '', $url)) > 1) {
    if (substr($_SERVER['REQUEST_URI'], 0, strlen($url)) == $url) {
      $cls = ' class="active-item"';
    }
  }
  else {
    // special case for home
    if($sf_request->getParameter('module')=='admin' && $sf_request->getParameter('action')=='planboard') {
      $cls = ' class="active-item"';
    }
  }
  ?>
  <li<?php echo $cls; ?>><a href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
  <?php
}
?>
</ul>
<?php } ?>