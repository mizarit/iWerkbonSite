<?php

class zeusRibbon
{
  private static $buttons = array();
  
  public static function addButton($button, $toolbar = 'Acties')
  {
    if (!$button) return;

    zeusRibbon::$buttons[$toolbar][] = $button;  
  }
  
  public static function get()
  {
    $ribbon = '';
    
    
    $ribbon .= <<<EOT
<div id="zeus-ribbon">
  <div id="zeus-toolbars">
    <div>\n
EOT;
    if (count(zeusRibbon::$buttons)) {
      foreach (zeusRibbon::$buttons as $toolbar => $buttons ) {
        $ribbon .= <<<EOT
      <div class="zeus-toolbar">
        <div>\n
EOT;

        foreach ($buttons as $button) {
          $ribbon .= $button->get();
        }
      
        $ribbon .= <<<EOT
          <p class="zeus-toolbar-name">{$toolbar}</p>
        </div>
      </div>\n
EOT;
    }
    }
    $ribbon .= <<<EOT
    </div>
  </div>
</div>
<script type="text/javascript">
var modalBoxTimer = false;

var zeusRibbon = {  
  startBox: function(box_id)
  {
    $(box_id+'-container').style.display = 'block';
    modalBoxTimer = true;
    Event.observe($(box_id+'-inner-container'), 'mouseout', function()
    { 
      setTimeout('zeusRibbon.checkBox(\''+box_id+'\')', 500);
      modalBoxTimer = false;
    });
    
    Event.observe($(box_id+'-inner-container'), 'mouseover', function()
    { 
      modalBoxTimer = true;
    });
    
  },
  
  checkBox: function(box_id)
  {
    if(!modalBoxTimer) {
      $(box_id+'-container').style.display = 'none';
    }
  }
}
</script>
\n
EOT;
    
   
    return $ribbon;
  }
  
  public static function getButtons($category = null)
  {
    return $category ? zeusRibbon::$buttons[$category] : zeusRibbon::$buttons;
  }
}