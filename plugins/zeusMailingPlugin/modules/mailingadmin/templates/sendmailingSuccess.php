<?php if ($needs_validation) { ?>
<div class="mailing-summary">
<p>De nieuwsbrief zal verzonden worden aan:</p>
<form action="#" id="zeus-1" method="post">
  <fieldset>
    <legend>Validatie formulier</legend>

    <table cellspacing="0" cellpadding="0" class="summary">
      <tr>
        <td><img src="/zeusCore/img/icons/famfamfam/accept.png" alt="Verzenden aan deze gebruiker" title="Verzenden aan deze gebruiker"></td>
        <td><img src="/zeusCore/img/icons/famfamfam/cross.png" alt="Niet verzenden aan deze gebruiker" title="Niet verzenden aan deze gebruiker"></td>
        <td colspan="3"></td>
      </tr>
      <tr>
        <td><input class="checkbox" name="toggle" type="radio" checked="checked" onclick="toggleAll(true);"></td>
        <td><input class="checkbox" name="toggle" type="radio" onclick="toggleAll(false);"></td>
        <td colspan="3">Alle inschrijvingen</td>
      </tr>
<?php foreach ($mailing_subscriptions as $mailing_subscription) { ?>
      <tr>
        <td style="width:22px"><input value="pending" class="checkbox subscription-check-a" id="subscription-<?php echo $mailing_subscription->getId(); ?>-a" name="subscription[<?php echo $mailing_subscription->getId(); ?>]" type="radio" <?php if($mailing_subscription->getStatus() == 'validate') echo ' checked="checked"';?>></td>
        <td style="width:22px"><input value="dontsend" class="checkbox subscription-check-b" id="subscription-<?php echo $mailing_subscription->getId(); ?>-b" name="subscription[<?php echo $mailing_subscription->getId(); ?>]" type="radio" <?php if($mailing_subscription->getStatus() == 'pending') echo ' checked="checked"';?>></td>
        <td style="width: 240px;"><?php echo $mailing_subscription->getMailinguser()->getEmail(); ?></td>
        <td><?php echo $mailing_subscription->getMailinguser()->getTitle(); ?></td>
      </tr>
<?php } ?>
    </table>
  </fieldset>
</form>


<script type="text/javascript">
function toggleAll(what)
{
  var wwhat = what;
  $$('.summary .subscription-check-a').each(function(s,i) {
    s.checked = wwhat;
  });
  
  $$('.summary .subscription-check-b').each(function(s,i) {
    s.checked = !wwhat;
  });
}

function sendMailing()
{
  //$('zeus-1').action = '<?php echo url_for('mailingadmin/sendmailing?id='.$sf_params->get('id')); ?>';
  $('zeus-1').submit();
}
</script>
</div>
<?php } else { ?>
<div style="margin: 150px auto;border: #cecece 1px dotted; padding:10px; width: 220px;" id="progress-container-outer">
  <div style="text-align:center;width: 220px;" id="status-container"></div>
  <div id="progress-container" style="background: url(/zeusMailing/img/progress-bar-bkg.png); height: 19px;width: 220px;">
    <div id="progress-inner-container" style="background: url(/zeusMailing/img/progress-bar.gif); height: 19px; width: 0px;"></div>
  </div>
</div>
<script type="text/javascript">
Event.observe(window, 'load', function() {
  getStatus();
});

function getStatus()
{
  new Ajax.Request('<?php echo url_for('mailingadmin/updatestatus'); ?>', {
    onComplete: function(response)
    {
      eval('var r = ' + response.responseText + ';');
      
      $('status-container').innerHTML = r.status;
      $('progress-inner-container').style.width = ((r.percentage / 100) * 220) + 'px';
      if (!r || !r.ready) {
        getStatus();
      }
      else {
        $('progress-container').style.display = 'none';
      }
    }
  });
}

</script>
<?php } ?>