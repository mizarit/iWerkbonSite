<?php

function zeus_help($object = null, $config = array())
{
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SlidingPager.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SliderTip.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PanelResizer.js');
	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PagingMemoryProxy.js');
  $response->addJavascript('/zeusCore/js/zeus-help/zeus-help.js');
  
  ob_start();
  ?>
  <div id="help-win" class="x-hidden">
    <div class="x-window-header">Help</div>
    <div id="help-tabs">
      <div id="zeus-help" class="x-tab" title="Ondersteuning bij het gebruiken van het CMS">
        <p style="margin-top:50px;text-align:center;"><img src="/zeusCore/img/ajax-loader.gif" alt="Bezig met laden..."></p>
      </div>
    </div>
  </div>
<script type="text/javascript">
var zhelp = new zeusHelp({});


</script>
  <?php
  
  return ob_get_clean();
}