<?php 

$config = zeusYaml::load('admin.yml');
if (isset($config['edit'])) {
  use_helper('ZeusEdit');
  
  if ($sf_request->getParameter('action') == 'edit') {
    if (isset($config['edit']['title_edit']) && $sf_params->get('id') > 0) {
      echo '<h2>'.$config['edit']['title_edit'].'</h2>'.PHP_EOL;
    }
    elseif (isset($config['edit']['title_create'])) {
      echo '<h2>'.$config['edit']['title_create'].'</h2>'.PHP_EOL;
    }
  }
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
  $config['edit']['update_url'] = url_for($sf_params->get('module').'/update');
  
  echo zeus_edit($object, $config['edit']);
}
else {
  throw new sfException('No edit section found in admin.yml');
}

include_component('core', 'helpers');
?> 