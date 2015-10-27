<?php

function files_upload_button($config = array())
{
  $uploader = files_upload_button_html($config);

  return new zeusRibbonButton(array(
    'type'  => 'html',
    'content' => $uploader
  ));
}

function files_upload_button_html($config = array())
{
  ob_start();
  $update_url = isset($config['update_url']) ? $config['update_url'] : url_for('filesadmin/update');
  $update_container = isset($config['update_container']) ? $config['update_container'] : 'files-container';
  $update_handler = isset($config['update_handler']) ? $config['update_handler'] : 'uploadComplete';
?>
<script type="text/javascript">
var swfu;

Event.observe(window, 'load', function() {
  var settings = {
    flash_url : "/zeusCore/js/swfupload/swfupload.swf",
    upload_url: "<?php echo url_for('filesadmin/upload') ?>",
    post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
    file_size_limit : "100 MB",
    file_types : "*.*",
    file_types_description : "Alle bestanden",
    file_upload_limit : 100,
    file_queue_limit : 0,
    custom_settings : {
      progressTarget : "upload-progress",
      cancelButtonId : "annuleer-button",
      updateUrl: "<?php echo $update_url; ?>",
      fileContainer: "<?php echo $update_container; ?>"
    },
    debug: false,

    button_image_url: "/zeusCore/js/swfupload/upload-button.png",
    button_width: "53",
    button_height: "24",
    button_placeholder_id: "upload-button",
   
    file_queued_handler : fileQueued,
    file_queue_error_handler : fileQueueError,
    file_dialog_complete_handler : fileDialogComplete,
    upload_start_handler : uploadStart,
    upload_progress_handler : uploadProgress,
    upload_error_handler : uploadError,
    upload_success_handler : uploadSuccess,
    upload_complete_handler : <?php echo $update_handler; ?>,
    queue_complete_handler : queueComplete  // Queue plugin event
  };

  swfu = new SWFUpload(settings);
  
  if ($('delete-btn')) {
    $('delete-btn').addClassName('zeus-button-disabled');
  }
});

<?php
	$response = sfContext::getInstance()->getResponse();
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addStylesheet('/zeusCore/css/zeus-dataview-screen.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	
	$m1 = $m2 = '';
	if (!isset($config['custom-dataview'])) {
	  $response->addJavascript('/zeusCore/js/zeus-dataview/zeus-dataview.js');
	}
	else {
	  $m1 = ' style="margin:10px 0 0 10px;"';
	  $m2 = ' style="position: absolute;top:0;left:150px;"';
	}
	$response->addJavascript('/zeusCore/js/swfupload/fileprogress.js');
	$response->addJavascript('/zeusCore/js/swfupload/handlers.js');
	$response->addJavascript('/zeusCore/js/swfupload/swfupload.js');
	
	
?>


</script>
<form <?php echo $m1; ?> action="<?php echo url_for('filesadmin/upload') ?>" method="post" enctype="multipart/form-data">
  <fieldset>
    <legend>Upload formulier</legend>
	  <div id="upload-button"></div>
	  <button id="annuleer-button" type="button" onclick="swfu.cancelQueue();" disabled="disabled"><div>Annuleren</div></button>
	  
	  <div class="fieldset flash" id="upload-progress"<?php echo $m2; ?>></div>
	  <div id="upload-status"></div>
  </fieldset>
</form>
<?php
  
  $uploader = ob_get_clean();
  
  return $uploader;
}

function files_container($cfg = array())
{
  return '<div id="files-container"></div>';
}

function files_delete_button($config = array())
{
  return new zeusRibbonButton(array(
    'label' => 'Verwijderen', 
    'icon'  => 'fileclose',
    'type'  => 'large', 
    'id'    => 'delete-btn',
    'callback' => 'panel.deleteItems(this)'
  ));
}

function files_browser($config = array())
{
  $response = sfContext::getInstance()->getResponse();
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addStylesheet('/zeusCore/css/zeus-dataview-screen.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/zeus-filebrowser/zeus-filebrowser.js');
}




function files_upload_button_simple($field, $value, $config = array())
{
  $update_url = isset($config['update_url']) ? url_for($config['update_url']) : url_for('filesadmin/update');
  $update_container = isset($config['update_container']) ? $config['update_container'] : 'files-container';
  $update_handler = isset($config['update_handler']) ? $config['update_handler'] : 'uploadComplete';
  
  ob_start();
?>
<script type="text/javascript">
var swfu;

Event.observe(window, 'load', function() {
  var settings = {
    flash_url : "/zeusCore/js/swfupload/swfupload.swf",
    upload_url: "<?php echo url_for('filesadmin/upload') ?>",
    post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
    file_size_limit : "100 MB",
    file_types : "*.*",
    file_types_description : "Alle bestanden",
    file_upload_limit : 100,
    file_queue_limit : 0,
    custom_settings : {
      progressTarget : "upload-progress",
      cancelButtonId : "annuleer-button",
      updateUrl: "<?php echo $update_url ?>",
      fileContainer: "<?php echo $update_container; ?>",
      fileField: "<?php echo $field; ?>"
    },
    debug: false,

    button_image_url: "/zeusCore/js/swfupload/upload-button.png",
    button_width: "53",
    button_height: "24",
    button_placeholder_id: "upload-button",
   
    file_queued_handler : fileQueued,
    file_queue_error_handler : fileQueueError,
    file_dialog_complete_handler : fileDialogComplete,
    upload_start_handler : uploadStart,
    upload_progress_handler : uploadProgress,
    upload_error_handler : uploadError,
    upload_success_handler : uploadSuccess,
    upload_complete_handler : <?php echo $update_handler; ?>,
    queue_complete_handler : queueComplete  // Queue plugin event
  };

  swfu = new SWFUpload(settings);
  
});

<?php

	$response = sfContext::getInstance()->getResponse();
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/swfupload/fileprogress.js');
	$response->addJavascript('/zeusCore/js/swfupload/handlers.js');
	$response->addJavascript('/zeusCore/js/swfupload/swfupload.js');
?>


</script>
<div style="margin-left: 150px;">
<form action="<?php echo url_for('filesadmin/upload') ?>" method="post" enctype="multipart/form-data">
  <fieldset>
    <legend>Upload formulier</legend>
	  <div id="upload-button"></div>
	  <button id="annuleer-button" type="button" onclick="swfu.cancelQueue();" disabled="disabled"><div>Annuleren</div></button>
	  
	  
	  <div class="simple" id="upload-progress" style="width: 100px;"></div>
	  <div id="upload-status"></div>
  </fieldset>
</form>
</div>
<?php
  
  $uploader = ob_get_clean();
  
  return $uploader;
}
