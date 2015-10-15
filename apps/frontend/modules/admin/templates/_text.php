<?php
$textObject = PartialPeer::findByName($key);
$text = $textObject ? $textObject->getText() : $text;
$t = isset($tag) ? $tag : 'p';
if (!isset($extra)) $extra = '';
if (strstr($_SERVER['SERVER_NAME'], 'dev')) { ?>
  <<?php echo $t; ?> id="<?php echo $key; ?>" class="inplace-editor"><?php echo $text; ?><?php echo $extra; ?></<?php echo $t; ?>>
  <script type="text/javascript">
    new Ajax.InPlaceEditor('<?php echo $key; ?>', '<?php echo url_for('admin/textEditor'); ?>', {
      cancelText: 'Annuleren',
      okText: 'Opslaan'
    });
  </script>
<?php
}
else if($text != '...') { ?>
  <<?php echo $t; ?> id="<?php echo $key; ?>"><?php echo $text; ?><?php echo $extra; ?></<?php echo $t; ?>>
<?php } ?>
