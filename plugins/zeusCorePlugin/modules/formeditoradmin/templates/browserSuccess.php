<form id="popup-form" style="margin:10px;position:relative;">
  <fieldset>
    <legend>Popup</legend>
  
    <p>Selecteer een formulier om in te voegen:</p>
<?php 
if ($forms) {
  $first = true;
  foreach ($forms as $form) { 
    $checked = $first ? ' checked="checked"' : '';
    $first = false;
    ?>
  
  <input<?php echo $checked; ?> type="radio" value="<?php echo $form->getId(); ?>" name="form-id" id="form-id-<?php echo $form->getId(); ?>" class="checkbox"> <label for="form-id-<?php echo $form->getId(); ?>"><?php echo $form->getTitle(); ?> formulier</label><br>
<?php } 
}
else {
  echo '<p><strong>Er zijn geen formulieren geconfigureerd.</strong></p>';
}
?>
<div style="clear:both;margin:5px;"></div>
<div style="position:absolute;top:200px;width:500px;padding-top:10px;text-align:right;border-top:#cecece 1px solid;">
<button type="button" class="zeus-button-disabled" id="insert-form-btn" onclick="insertFormLocal();"><div>Invoegen</div></button>
<button type="button" id="close-form-btn" onclick="window.close();"><div>Sluiten</div></button>
</div>
</fieldset>
</form>
<script type="text/javascript">
function $RF(el, radioGroup) {
if($(el).type && $(el).type.toLowerCase() == 'radio') {
  var radioGroup = $(el).name;
  var el = $(el).form;
} else if ($(el).tagName.toLowerCase() != 'form') {
  return false;
 }
 
 var checked = $(el).getInputs('radio', radioGroup).find(
 function(re) {return re.checked;}
);
 return (checked) ? $F(checked) : null;
}


insertFormLocal = function()
{
  v = $RF('popup-form', 'form-id');
  window.opener.insertForm(v);
}
</script>