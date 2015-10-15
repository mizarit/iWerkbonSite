        <form action="<?php echo url_for('mailing/subscribe'); ?>" method="post" id="newsletter-form">
          <fieldset>
            <legend>Nieuwsbrief</legend>
            <input type="text" name="email" id="email" value="je@e-mail.adres" onclick="if(this.value=='je@e-mail.adres')this.value='';">
            <input type="hidden" name="mailinglist" id="mailinglist" value="<?php echo $mailinglist; ?>">
            <select name="modus" id="modus">
              <option value="subscribe"><?php echo __('aanmelden'); ?></option>
              <option value="unsubscribe"><?php echo __('afmelden'); ?></option>
            </select>
            <button type="submit"><div><?php echo __('Verzenden'); ?></div></button>
          </fieldset>
        </form>