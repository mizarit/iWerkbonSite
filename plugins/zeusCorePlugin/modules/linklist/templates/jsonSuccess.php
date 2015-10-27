var tinyMCELinkList = new Array(
<?php
$l = '';
foreach ($list as $key => $value) {
  $l .= '  ["'.$value.'", "'.$key.'"],'."\n";
  
}
echo trim(trim($l), ',');
?> 
);
