<div id="direct-login">
  <select name="site" id="site" onchange="window.location.href=this.value;">
<?php
$user = UserPeer::retrieveByPk(sfContext::getInstance()->getUser()->getAttribute('userid'));

$username = $user->getUsername();
$password = $user->getPassword();

$salt = substr(md5(time()),5,6);
$secret = zeusConfig::get('Multisite', 'Salt', 'input', 'secret');

$hash = md5($username.$password.$salt.$secret);

$env = sfConfig::get('sf_environment') == 'dev' ? '/backend_dev.php' : '';
$sites = sfConfig::get('app_multisite_cms');
foreach ($sites as $site) {
  $selected = isset($site['active']) ? ' selected="selected"' : '';
  $url = strpos($_SERVER['HTTP_HOST'], '.mizar-it') ? $site['dev']['url'] : $site['prod']['url'];
?>
    <option <?php echo $selected; ?> value="<?php echo $url.$env; ?>/multisite/directlogin/username/<?php echo $username; ?>/salt/<?php echo $salt; ?>/hash/<?php echo $hash; ?>"><?php echo $site['title']; ?></option>
<?php } ?>
  </select>
</div>