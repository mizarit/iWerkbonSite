
<div style="width: 600px;margin: 40px auto; border:#cecece 2px dotted;padding:20px;">
  <div id="logo2"><h1><a href="<?php echo url_for('@homepage') ?>">Zeus<span>CMS4</span></a></h1></div>
<p>Welkom bij het content management systeem. Om verder te gaan dient u zich aan te melden met de aan u verstrekte inloggegevens.</p>
<form action="<?php echo url_for('securityadmin/login') ?>" method="post">
  <fieldset>
    <legend>Inloggen</legend>
<?php 
if ($form->hasErrors()) { 
?>
      <ul class="form-errors">
<?php 
  foreach ($form as $key => $field) {
    echo $field->renderError();
  } 
?>
      </ul>
<?php 
} 

echo $form 

?>
    <div class="form-button">
      <button type="submit"><div>Inloggen</div></button>
    </div>
  </fieldset>
</form>
</div>
<style type="text/css">
#logo {
  display: none;
}
</style>