<?php
use_helper('ZeusEdit');
$title = $config['wizard']['title'].': ';
$stepConfig = $config['wizard']['steps'][$step];
$title .= $stepConfig['title'];
$stepC = array_search($step, array_keys($config['wizard']['steps']->getRawValue()));
$title .= ' ( '.($stepC+1).' / '.count($config['wizard']['steps']).' )';
echo '<h2>'.$title.'</h2>'.PHP_EOL;

if (isset($errorList) && count($errorList->getRawValue()) > 0) {
    echo '<ul class="form-errors">';
    foreach ($errorList->getRawValue() as $error) {
      if ($error == '') continue;
      echo '<li>'.$error.'</li>';
    }
    echo '</ul>';
    ?>
<script type="text/javascript">
Event.observe(window, 'load', function() {
  var errorFields = <?php echo json_encode(array_keys($errorList->getRawValue())); ?>;
  errorFields.each(function(s,i) {
    if ($(s)) {
      var formRow = ($(s).parentNode);
      if (formRow.hasClassName('form-row')) {
        formRow.addClassName('error');
      }
      else {
        $(s).addClassName('error');
      }
    }
  
  });
});
</script>
<?php
}

$config = $config->getRawValue();
$config['wizard']['buttons']['save']['disable'] = true;
$config['wizard']['update_url'] = url_for($sf_params->get('module').'/wizard');
$config['wizard']['fields'] = $config['wizard']['steps'][$step]['fields'];
$config['wizard']['fields']['step'] = array('type' => 'hidden', 'value' => $step);
$config['wizard']['fields']['modus'] = array('type' => 'hidden', 'value' => 'next');
echo zeus_edit($object, $config['wizard']);

$buttons = zeusRibbon::getButtons('Acties');
$prev = $next = false;
$prevCallback = $nextCallback = '';
$nextLabel = 'Volgende &raquo;';
foreach ($buttons as $button) {
  $cfg = $button->getConfig();
  if ($cfg['label'] == 'Vorige') {
    $prev = !$cfg['disabled'];
    $prevCallback = $cfg['callback'];
  }
  if ($cfg['label'] == 'Volgende' || $cfg['label'] == 'Opslaan') {
    $next = !isset($cfg['disabled']) || !$cfg['disabled'];
    $nextCallback = $cfg['label'] == 'Volgende' ? $cfg['callback'] : "$('zeus-1').submit()";
    $nextLabel = $cfg['label'] == 'Volgende' ? 'Volgende &raquo;' : 'Opslaan';
  }
}
?>
<div id="wizard-button-container">
  <button class="btn-prev<?php if (!$prev) echo ' disabled" disabled="disabled'; ?>" onclick="<?php echo $prevCallback; ?>"><div> &laquo; Vorige</div></button>
  <button class="btn-next<?php if (!$next) echo ' disabled" disabled="disabled'; ?>" onclick="<?php echo $nextCallback; ?>"><div><?php echo $nextLabel; ?></div></button>
</div>