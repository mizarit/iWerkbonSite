<?php if (!$sf_user->getAttribute('admin')) { ?>
<script type="text/javascript">
// Google Analytics
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo zeusConfig::get('Google Analytics', 'Google Analytics account', 'input', ''); ?>']);
_gaq.push(['_setDomainName', '<?php echo zeusConfig::get('Google Analytics', 'Site domain', 'input', '.health-challenge.nl'); ?>']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<meta name="google-site-verification" content="<?php echo zeusConfig::get('Site verification', 'Google', 'input', 'LCh2PtWgXPm8XyzCLekySyli2Eaoubji2luboUYeMtE'); ?>" />
<meta name="alexaVerifyID" content="<?php echo zeusConfig::get('Site verification', 'Alexa', 'input', 'Vz8TTAnDBnqaTSNKYlhVQpvN40k'); ?>" />  
<meta name="msvalidate.01" content="<?php echo zeusConfig::get('Site verification', 'Microsoft Bing', 'input', '944BB9B878DB203F6E43B9E95F670296'); ?>" />
<?php } ?>
