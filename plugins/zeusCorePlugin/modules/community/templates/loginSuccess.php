<h1>Inloggen</h1>
<form action="#" method="post">
  <fieldset>
    <legend>Login</legend>
      <input type="hidden" name="formname" value="login">
    
    <?php if (count($errors) > 0) { 
      echo '<ul class="error-list">';
      foreach ($errors as $field => $error) {
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
    } ?>
        
    <div class="form-row">
      <div class="form-label"><label for="username">Gebruikersnaam</label></div>
      <input type="text" name="username" id="username" value="<?php echo $sf_params->get('username'); ?>">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="password">Wachtwoord</label></div>
      <input type="password" name="password" id="password">
    </div>
    
    <div class="form-row">
      <input class="checkbox" type="checkbox" name="remember" id="remember"<?php if ($sf_params->has('remember')) echo ' checked="checked"'; ?>> <label for="remember">Onthoud mijn inloggegevens</label>
    </div>
    
    <div class="form-button">
      <button type="submit">Inloggen</button>
    </div>
  </fieldset>
</form>