<div id="admin-content">
  <?php include_component('admin', 'text', array('key' => 'thankyou-help-title1a', 'text' => 'Bedankt!', 'tag' => 'h1')); ?>
  <?php include_component('admin', 'text', array('key' => 'thankyou-help1a', 'text' => '...')); ?>

  <button onclick="window.location.href='<?php echo url_for('admin/login'); ?>';" class="button-2">Inloggen</button>
</div>