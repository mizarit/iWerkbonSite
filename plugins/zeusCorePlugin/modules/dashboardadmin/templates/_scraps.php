<h2>Notities</h2>
<form action="#" method="post" style="position: relative;">
  <fieldset>
    <legend>scraps</legend>
    <img onclick="saveScraps();" style="cursor:pointer;position: absolute;right:0;top:-20px;" src="/zeusCore/img/icons/famfamfam/disk.png" alt="Opslaan" title="Opslaan">
    <textarea name="scraps" id="scraps" rows="10" cols="10" style="border:#cecece 1px dotted;width:445px;height:160px;"><?php echo $user->getScraps(); ?></textarea>
  </fieldset>
</form>
<script type="text/javascript">
function saveScraps() {
  $('scraps').disabled = true;
  new Ajax.Request('<?php echo url_for('core/scraps'); ?>', {
    parameters: { scraps: $('scraps').value },
    method: 'post',
    onSuccess: function(t) {
      $('scraps').disabled = false;
    }
  });
}
</script>