<h1>Registreren</h1>
<p>Gebruik onderstaand formulier om u eenmalig te registreren. Registratie is uiteraard <strong>gratis</strong> en dient slecht om te controleren of u een fysiek persoon bent. Hierna kunt u gebruik maken van alle functionaliteiten op deze website.</p>
<form action="#" method="post">
  <fieldset>
    <legend>Registreren</legend>
      <input type="hidden" name="formname" value="register">
    
    <?php if (count($errors) > 0) { 
      echo '<ul class="error-list">';
      foreach ($errors as $field => $error) {
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
    } ?>
        
    <div class="form-row">
      <div class="form-label"><label for="name">Naam</label></div>
      <input type="text" name="name" id="name" value="<?php echo $sf_params->get('name'); ?>">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="username">Gebruikersnaam</label></div>
      <input type="text" name="username" id="username" value="<?php echo $sf_params->get('username'); ?>">
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="password">Wachtwoord</label></div>
      <input type="password" name="password" id="password">
    </div>
    
    <div class="form-row form-row-wide">
      <div class="form-label"><label for="password-2">Nogmaals</label></div>
      <input type="password" name="password-2" id="password-2"> ( voer nogmaals het wachtwoord ter controle in )
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="email">E-mail adres</label></div>
      <input type="email" name="email" id="email">
    </div>
    
    <div class="form-row form-row-wide">
      <input class="checkbox" type="checkbox" name="accept" id="accept"<?php if ($sf_params->has('accept')) echo ' checked="checked"'; ?>> <label for="accept">Ik accepteer de <a href="<?php echo route_for('Page:25'); ?>">algemene voorwaarden</a> en ik heb de <a href="<?php echo route_for('Page:26'); ?>">gedragsregels</a> gelezen.</label>
    </div>
    
    <div class="form-button">
      <button type="submit">Registreren</button>
    </div>
  </fieldset>
</form>