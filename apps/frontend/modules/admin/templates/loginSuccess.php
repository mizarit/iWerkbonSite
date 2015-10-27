
<div style="width: 33em;margin: 40px auto; border:#cecece 2px dotted;padding:20px;">
  <p style="text-align:center;"><img src="/img/logo-iwerkbon.png"></p>
  <p>Welkom bij iWerkbon. Om verder te gaan dient u zich aan te melden met de aan u verstrekte inloggegevens.</p>
  <form action="<?php echo url_for('admin/login') ?>" method="post" id="login-form">
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
      <div class="form-button" style="border-top: #cecece 1px solid;text-align:right;padding-top: 1em;">
        <p style="text-align:left;margin:0;float:left;"><button type="button" class="button-4" onclick="window.location.href='<?php echo url_for('admin/register'); ?>';">Registreren</button></p>
        <button type="submit" class="button-2">Inloggen</button>
      </div>
    </fieldset>
  </form>
</div>
<style type="text/css">
  #logo {
    display: none;
  }
  #nav-container {
    display: none;
  }
</style>
<script type="text/javascript">
  localStorage.setItem('expire_appointment-list', 0);
  localStorage.setItem('expire_customer-list', 0);
  localStorage.setItem('expire_invoice-list', 0);
  localStorage.setItem('expire_planboard-list', 0);
  localStorage.setItem('expire_workorder-list', 0);
</script>