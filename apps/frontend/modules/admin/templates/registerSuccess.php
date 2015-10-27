<div id="admin-content">
<?php include_component('admin', 'text', array('key' => 'register-help-title1a', 'text' => 'iWerkbon account aanmaken', 'tag' => 'h1')); ?>
<?php include_component('admin', 'text', array('key' => 'register-help1a', 'text' => '...')); ?>
<form action="#" method="post" id="register-form">
  <fieldset>
    <legend>Settings form</legend>
    <?php include_component('admin', 'text', array('key' => 'register-help-title1', 'text' => 'Beheerder', 'tag' => 'h2')); ?>
    <?php include_component('admin', 'text', array('key' => 'register-help1', 'text' => '...')); ?>

    <div class="form-row">
      <div class="form-label"><label for="admin-title">Naam</label></div>
      <input<?php if(isset($errors['admin-title'])) echo ' class="error"'; ?> type="text" name="admin-title" id="admin-title" value="<?php echo $sf_params->get('admin-title'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="admin-email">E-mail adres</label></div>
      <input<?php if(isset($errors['admin-email'])) echo ' class="error"'; ?> type="text" name="admin-email" id="admin-email" value="<?php echo $sf_params->get('admin-email'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="admin-username">Gebruikersnaam</label></div>
      <input<?php if(isset($errors['admin-username'])) echo ' class="error"'; ?> type="text" name="admin-username" id="admin-username" value="<?php echo $sf_params->get('admin-username'); ?>" style="width:7em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="admin-password1">Wachtwoord</label></div>
      <input<?php if(isset($errors['admin-password1'])) echo ' class="error"'; ?> type="password" name="admin-password1" id="admin-password1" style="width:7em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="admin-password2">Wachtwoord controle</label></div>
      <input<?php if(isset($errors['admin-password2'])) echo ' class="error"'; ?> type="password" name="admin-password2" id="admin-password2" style="width:7em;">
    </div>



    <?php include_component('admin', 'text', array('key' => 'register-help-title1', 'text' => 'Bedrijfsgegevens', 'tag' => 'h2')); ?>
    <?php include_component('admin', 'text', array('key' => 'register-help1', 'text' => '...')); ?>

    <div class="form-row">
      <div class="form-label"><label for="companyname2">Bedrijfsnaam</label></div>
      <input<?php if(isset($errors['companyname2'])) echo ' class="error"'; ?> type="text" id="companyname2" name="companyname2" value="<?php echo $sf_params->get('companyname2'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="address">Adres</label></div>
      <input<?php if(isset($errors['address'])) echo ' class="error"'; ?> type="text" id="address" name="address" value="<?php echo $sf_params->get('address'); ?>" style="width:11em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="zipcode">Postcode & plaats</label></div>
      <input<?php if(isset($errors['zipcode'])) echo ' class="error"'; ?> type="text" id="zipcode" name="zipcode" value="<?php echo $sf_params->get('zipcode'); ?>" style="width:4em;"> <input<?php if(isset($errors['city'])) echo ' class="error"'; ?> type="text" id="city" name="city" value="<?php echo $sf_params->get('city'); ?>" style="width:12em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="phone1">Telefoonnummer</label></div>
      <input<?php if(isset($errors['phone1'])) echo ' class="error"'; ?> type="text" id="phone1" name="phone1" value="<?php echo $sf_params->get('phone1'); ?>" style="width:8em;">
    </div>

    <?php include_component('admin', 'text', array('key' => 'register-help-title4', 'text' => 'OnlineAfspraken.nl koppeling', 'tag' => 'h2')); ?>
    <?php include_component('admin', 'text', array('key' => 'register-help4', 'text' => '...')); ?>

    <div class="form-row">
      <div class="form-label"><label for="api_server">Server</label></div>
      <input<?php if(isset($errors['api_server'])) echo ' class="error"'; ?> type="text" id="api_server" name="api_server" value="<?php echo $sf_params->has('api_server') ? $sf_params->get('api_server') : 'https://agenda.onlineafspraken.nl/APIREST'; ?>" style="width:19em;">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="api_key">API key</label></div>
      <input<?php if(isset($errors['api_key'])) echo ' class="error"'; ?> type="text" id="api_key" name="api_key" value="<?php echo $sf_params->get('api_key'); ?>" style="width:10em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="api_secret">API secret</label></div>
      <input<?php if(isset($errors['api_secret'])) echo ' class="error"'; ?> type="text" id="api_secret" name="api_secret" value="<?php echo $sf_params->get('api_secret'); ?>" style="width:23em;">
    </div>
  </fieldset>
</form>
</div>