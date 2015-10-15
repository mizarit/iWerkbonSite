<?php
if ($sf_user->isAuthenticated()) {
//var_dump($sf_user);
?>
Welkom <?php echo $sf_user->getAttribute('usertitle'); ?> <a href="<?php echo url_for('admin/settings'); ?>#login" title="Instellingen"><i class="fa fa-cog" style="font-size:1.3em;"></i></a> <a href="<?php echo url_for('admin/logoff'); ?>" title="Uitloggen"><i class="fa fa-sign-out" style="font-size:1.3em;"></i></a>
<?php } ?>