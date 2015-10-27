<div style="width: 760px;overflow:scroll-y">
  <div style="float:left;width: 90px;">&nbsp;</div>
  <div style="float:left;width: 330px;"><strong>Huidige versie</strong></div>
  <div style="float:left;width: 10px;">&nbsp;</div>
  <div style="float:left;width: 330px;"><strong>Oude versie</strong></div>
  <div style="clear:both;border-bottom: #cecece 1px dotted;margin: 2px 0;"></div>
  
<?php foreach ($fields as $key => $label) { ?>
  <div style="float:left;width: 90px;"><?php echo $label; ?></div>
  <div style="float:left;width: 330px;"><?php echo nl2br(trim($values1[$key])); ?></div>
  <div style="float:left;width: 10px;">&nbsp;</div>
  <div style="float:left;width: 330px;"><?php echo nl2br(trim($values2[$key])); ?></div>
  <div style="clear:both;border-bottom: #cecece 1px dotted;margin: 2px 0;"></div>
<?php } ?>
</div>

<div style="clear:both;"></div>

<?php

$diff = new zeusAnalyzer();

if (count($analyzers) > 0) {
  foreach ($analyzers as $key) {
    $t1 = $values1[$key];
    $t2 = $values2[$key];
    $text = $diff->inline($t2, $t1,2);
  
    echo count($diff->changes).' wijziging';
    if (count($diff->changes) != 1) echo 'en';
    echo $text;
  }
}