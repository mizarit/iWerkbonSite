<?php
$subnav = $sf_user->getAttribute('subnav');
$anchors = array();
foreach ($subnav as $nav) {
  if (isset($nav['title'])) {
    echo '<h2>'.$nav['title'].'</h2>';
  }
  if (isset($nav['items'])) {
    echo '<ul>';
    foreach ($nav['items'] as $key => $value) {
      if (is_numeric($key)) {
        echo '<li>'.$value.'</li>';
      }
      else if ($key == 'hook') {
        echo '<li id="hook"></li>';
      }
      else {
        if (is_array($value)) {
          $anchor = array_keys($value)[0];
          $label = array_values($value)[0];
          $anchors[$anchor] = $key;
          echo '<li class="subnav-item" id="subnav-'.$anchor .'"><a href="#' . $anchor . '" onclick="setActive(\''.$anchor.'\');' . $key . '">' . $label . '</a></li>';
        }
        else {
          echo '<li><a href="javascript:' . $key . '">' . $value . '</a></li>';
        }
      }
    }
    echo '</ul>';
  }
}
if (count($anchors) > 0) {
?>
<script type="text/javascript">
  var anchors = <?php echo json_encode($anchors); ?>;
  Event.observe(window, 'load', function() {
    var hash = window.location.hash.substring(1);
    if(anchors[hash]) {
      eval(anchors[hash]);
      setActive(hash);
    }
  });
  function setActive(which) {
    var wwhich = which;
    $$('.subnav-item').each(function(s,i) {
      t = s.id.substr(7);
      if(t ==wwhich) {
        s.addClassName('active-item');
      }
      else {
        s.removeClassName('active-item');
      }
    });
  }
</script>
<?php } ?>