<?php include_partial('global/header'); ?>
  <div id="content">
    <div id="subnav">
      <?php include_component('admin', 'subnav'); ?>
      <div style="clear:both;"></div>
    </div>
    <div id="content-inner">
<?php echo $sf_content; ?>
    </div>
    <div id="buttons">
      <div id="buttons-inner">
        <?php include_component('admin', 'buttons'); ?>
      </div>
    </div>
  </div>
  <div id="confirm-form" style="display:none;">
    <div class="modal-inner-micro" style="margin:10px;">
    <p id="confirm-caption"></p>
    </div>
    <div class="form-buttons">
      <button class="button-1">Annuleer</button>
      <button class="button-2">OK</button>

    </div>
  </div>
<?php include_partial('global/footer'); ?>