<?php

class zeusPropertiesFilter extends sfFilter
{
 
  public function execute($filterChain)
  {
    
    //sfContext::getInstance()->getConfiguration()->loadHelpers('Tag');
    //add_rss_link('@homepage', 'RSS Feed');
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute'); 
    $object = object_for_request();
    
    $properties = false;
    
    if ($object) {
      $object_key = get_class($object).':'.$object->getId();
      $c = new Criteria;
      $c->add(PropertiesPeer::OBJECT, $object_key);
      $properties = PropertiesPeer::doSelectOne($c);
      if ($properties) {
        sfContext::getInstance()->getResponse()->addMeta('title', $properties->getMetaTitle(), true);
        sfContext::getInstance()->getResponse()->addMeta('keywords', $properties->getMetaKeywords(), true);
        sfContext::getInstance()->getResponse()->addMeta('description', $properties->getMetaDescription(), true);
        
        if (!$properties->getManualkeyword() || $properties->getMetaKeywords() == '') {
          if ($properties->getMetaKeywords() == '') {
            if (method_exists($object, 'getContent')) {
              $props = zeusAnalyzer::getKeywords(strip_tags($object->getContent()));
            }
            else {
              $props = '';
            }
            
          }
          else {
            $props = '';
          }
          
          sfContext::getInstance()->getResponse()->addMeta('keywords', $props, true);
        }
        
      }
      else {
        sfContext::getInstance()->getResponse()->addMeta('keywords', '', true);
      }
    }
    
    $filterChain->execute();
    
    if ($properties) {

      $javascript = $properties->getJavascript();
    
      if ($javascript != '') {
        $content = $this->context->getResponse()->getContent();
        
        $content .= <<<EOT
<script type="text/javascript">
Event.observe(window, 'load', function() { 
{$javascript}
});
</script>\n
EOT;
        $this->context->getResponse()->setContent($content);  
      }
    }

    return;
  }
}