<?php
use_helper('Files');
echo files_upload_button_html(array(
  'update_container' => 'files-browser-container',
  'custom-dataview' => true
));

echo files_browser();
?>
<div id="files-browser-container" style="overflow: scroll;height:270px;"></div>
<div style="clear:both;margin:5px;"></div>
<button class="zeus-button-disabled" id="insert-btn" onclick="panel.useItem(this);"><div>Invoegen</div></button>
<script type="text/javascript">
var filebrowser_callback_field = '<?php echo $sf_params->get('field_name'); ?>';
var hostname = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>';
</script>