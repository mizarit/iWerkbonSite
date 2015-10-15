<ul>
<?php 

if ($sf_user->getAttribute('company_id') > 0) {
  $company = CompanyPeer::getLoggedInCompany();
  $test = url_for('lastminutesadmin/index'); $cls = substr($_SERVER['REQUEST_URI'], 0, strlen($test)) == $test ? ' class="active-item"' : '';
  
  $v1 = $sf_user->hasAttribute('from_partner');
  $v2 = $sf_user->hasAttribute('from_admin');
                
?>
                          <li><a href="<?php echo url_for('lastminutesadmin/logoff'); ?>"><?php echo $v1 ? 'Terug naar partner' : ($v2 ? 'Terug naar admin' : 'Uitloggen'); ?></a></li>
                          <!--<li>Je bent ingelogd als <?php echo $company->getTitle(); ?></li>-->
                          <li><a href="<?php echo url_for('lastminutesadmin/index'); ?>">Instellingen</a></li>

<?php 
}
else if($sf_user->hasAttribute('admin') && $sf_user->hasAttribute('user_id')) {

  $user = UserPeer::retrieveByPk($sf_user->getAttribute('user_id'));
  if ($sf_user->getAttribute('partner_id') > 0) {
    $partner = PartnerPeer::retrieveByPk($sf_user->getAttribute('partner_id'));
    if ($partner) {
      $user = UserPeer::retrieveByPk($partner->getUserId());
    }
  }
  else {
    $test = url_for('admins/index'); $cls = substr($_SERVER['REQUEST_URI'], 0, strlen($test)) == $test ? ' class="active-item"' : '';
  }
?>
                        <li><a href="<?php echo $sf_user->hasAttribute('partner_id') ? url_for('partners/logoff') : url_for('admins/logoff'); ?>"><?php echo $sf_user->hasAttribute('partner_id') ? 'Terug naar admin' : 'Uitloggen'; ?></a></li>
                        <!--<li>Je bent ingelogd als <?php echo isset($partner) && $partner ? $partner->getTitle() : $user->getTitle(); ?></li>-->
                        <li><a href="<?php echo $sf_user->hasAttribute('partner_id') ? url_for('partners/index') : url_for('admins/index'); ?>">Instellingen</a></li>

<?php 
} 
else if($sf_user->getAttribute('user_id') > 0 && $sf_user->hasAttribute('partner')) {

  $user = UserPeer::retrieveByPk($sf_user->getAttribute('user_id'));
  $c = new Criteria;
  $c->add(PartnerPeer::USER_ID, $user->getId());
  $partner = PartnerPeer::doSelectOne($c);
  $test = url_for('partners/index'); $cls = substr($_SERVER['REQUEST_URI'], 0, strlen($test)) == $test ? ' class="active-item"' : ''; ?>

                          <li><a href="<?php echo url_for('partners/logoff'); ?>">Uitloggen</a></li>
                          <!--<li>Je bent ingelogd als <?php echo $partner->getTitle(); ?></li>-->
                          <li><a href="<?php echo url_for('partners/index'); ?>">Instellingen</a></li>

<?php 
} 
else if($sf_user->getAttribute('user_id') > 0) { 
  $user = UserPeer::retrieveByPk($sf_user->getAttribute('user_id'));
  $c = new Criteria;
  $c->add(ConsumerPeer::USER_ID, $user->getId());
  $consumer = ConsumerPeer::doSelectOne($c);
  $test = url_for('consumers/index'); $cls = substr($_SERVER['REQUEST_URI'], 0, strlen($test)) == $test ? ' class="active-item"' : ''; ?>

                          <li><a href="<?php echo url_for('consumers/logoff'); ?>">Uitloggen</a></li>
                          <!--<li>Je bent ingelogd als <?php echo $consumer->getTitle(); ?></li>-->
                          <li><a href="<?php echo url_for('consumers/index'); ?>">Instellingen</a></li>

                          
<?php 
} 
else { 
  $cls = in_array($_SERVER['REQUEST_URI'], array(url_for('@lastminutes_user_login'))) ? ' class="active-item"' : ''; ?>

                      <li><a href="<?php echo url_for('lastminutesadmin/login'); ?>">Inloggen</a></li>

<?php 
} 
?>
                      <li><a href="<?php echo url_for('lastminutes_signup'); ?>">Bedrijven</a></li>
                      <li><a href="<?php echo url_for('folder/hoewerkthet'); ?>">Hoe werkt PlekjeVrij?</a></li>
</ul>