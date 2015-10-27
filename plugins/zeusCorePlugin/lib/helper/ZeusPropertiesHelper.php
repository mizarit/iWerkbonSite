<?php

function zeus_properties($object = null, $config = array())
{
  $response = sfContext::getInstance()->getResponse();
  $response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
  $response->addJavascript('/zeusCore/js/zeus-properties/zeus-properties.js');
  
  if (get_class($object) == 'sfOutputEscaperObjectDecorator') {
    $object = $object->getRawValue();
  }
  
  $key = get_class($object).':'.$object->getId();
  
  $c = new Criteria;
  $c->add(PropertiesPeer::OBJECT, $key);
  $properties = PropertiesPeer::doSelectOne($c);
  if (!$properties) {
    $properties = new Properties;
  }
  
  ob_start();
  ?>
  <div id="properties-win" class="x-hidden">
    <div class="x-window-header">Eigenschappen</div>
    <div id="properties-tabs">
       <div class="x-tab" title="Zoekmachine optimalisatie">
         <div class="form-row">
           <div class="form-label"><label for="meta-title">Meta titel</label></div>
           <input style="padding:2px;width: 300px;" value="<?php echo $properties->getMetaTitle(); ?>" type="text" name="meta-title" id="meta-title">
         </div>
         <div class="form-row">
           <div class="form-label"><label for="meta-description">Meta omschrijving</label></div>
           <input style="padding:2px;width: 300px;" value="<?php echo $properties->getMetaDescription(); ?>" type="text" name="meta-description" id="meta-description">
         </div>
         <div class="form-row">
           <div class="form-label"><label for="meta-keywords">Meta sleutelwoorden</label></div>
           <textarea cols="10" rows="10" style="height:100px;width: 500px;" name="meta-keywords" id="meta-keywords"><?php 
           if(!$properties->getManualkeyword()) {
             
           }
           else { 
             echo $properties->getMetaKeywords(); 
           } ?></textarea>
         </div>
         <div class="form-row">
           <div class="form-label"><label></label></div><input <?php if(!$properties->getManualkeyword()) echo ' checked="checked"'; ?> onchange="checkAutoKeywords(this);" type="checkbox" class="checkbox" name="meta-keywords-auto" id="meta-keywords-auto"> <label for="meta-keywords-auto">Bepaal automatisch de sleutelwoorden en synoniemen uit de tekst</label>
         </div>
      </div>
      
      <?php if ($object->getId()) { 
        use_helper('ZeusRoute');
        $url = route_for($object);
        $url = str_replace('/frontend_dev.php', '', $url);
        ?>
      <div class="x-tab" title="Toegankelijkheid">
        <div class="form-row">
          <div class="form-label"><label for="meta-url">URL</label></div>
          <input style="padding:2px;width: 300px;" value="<?php echo $url; ?>" type="text" name="meta-url" id="meta-url">
        </div>
        
        <div class="form-row">
          <div class="form-label"><label></label></div><input <?php if(!$properties->getExcludesitemap()) echo ' checked="checked"'; ?> type="checkbox" class="checkbox" name="meta-exclude" id="meta-exclude"> <label for="meta-exclude">Sluit dit object uit in de Google Sitemap zodat deze niet geïndexeerd wordt.</label>
        </div>
      </div>
      <?php } else { $url = ''; } ?>
      
      <div class="x-tab" title="Geavanceerd">
        <div class="form-row">
           <div class="form-label"><label for="meta-javascript">Javascript code</label></div>
           <textarea cols="10" rows="10" style="height:140px;width: 500px;" name="meta-javascript" id="meta-javascript"><?php echo $properties->getJavascript(); ?></textarea>
         </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  var properties = new zeusProperties({
    url: '<?php echo $url; ?>',
    manualkeyword: '<?php echo $properties->getManualkeyword() ? 1 : 0; ?>',
    excludesitemap: '<?php echo $properties->getExcludesitemap() ? 1 : 0; ?>',
    metatitle: '<?php echo $properties->getMetaTitle(); ?>',
    metadescription: '<?php echo $properties->getMetaDescription(); ?>',
    metakeywords: <?php echo json_encode(array('keywords' => $properties->getMetaKeywords())); ?>,
    javascript: <?php echo json_encode(array('javascript' => $properties->getJavascript())) ?>
  });
  
  function checkAutoKeywords(what)
  {
    if (what.checked) {
      $('meta-keywords').disabled = true;
      if ($('content')) {
        new Ajax.Request('<?php echo url_for('core/analyzer'); ?>', {
          parameters: {
            content: $('content').value
          },
          onSuccess: function(t)
          {
            $('meta-keywords').innerHTML = t.responseText;
            properties.Options.metakeywords.keywords = t.responseText;
          }
        });
      }
    }
    else {
      $('meta-keywords').disabled = false;
    }
  }

  <?php if(!$properties->getManualkeyword()) { ?>
  checkAutoKeywords($('meta-keywords-auto'));
  <?php } ?>

  </script>
<?php
  return ob_get_clean();
}