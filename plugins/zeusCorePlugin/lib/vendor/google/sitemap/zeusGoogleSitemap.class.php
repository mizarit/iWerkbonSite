<?php

class zeusGoogleSitemap 
{
  static public function generateSitemap()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');

    $routes = RoutePeer::doSelect(new Criteria());
    
    $ret = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n
EOT;
    
    $c = new Criteria;
    
    $date = date('Y-m-d');
    foreach ($routes as $route) {
      if (!in_array($route->getObject(), array('Page', 'News', 'Advert', 'Advertisement', 'Lastminute'))) continue;
    
      $peer = $route->getObject().'Peer';
      $object = call_user_func_array(array($peer, 'retrieveByPk'), array($route->getObjectId()));
      if (!$object) continue;
      
      if (method_exists($object, 'inSitemap')) {
        if (!$object->inSitemap()) continue;
      }
      
      // check if it was not excluded manually
      $c->clear();
      $c->add(PropertiesPeer::OBJECT, $route->getObject().':'.$route->getObjectId());
      $properties = PropertiesPeer::doSelectOne($c);
      if ($properties) {
        if ($properties->getExcludesitemap()) continue;
      }
      
      if ($route->getObject() == 'Lastminute') {
        if (substr($object->getModus(), 0, 9) != 'approved:') continue;
      
        $c = new Criteria;
        $c->add(CompanyPeer::STATUS, 4, Criteria::NOT_EQUAL);
        $companies = CompanyPeer::doSelect($c);
        $valid_company_ids = array();
        foreach ($companies as $company) {
          $valid_company_ids[] = $company->getId();
        }
        if (!in_array($object->getCompanyId(), $valid_company_ids)) continue;
        
        $c->clear();
        $c->add(LastminuteSaleschannelPeer::LASTMINUTE_ID, $object->getId());
        $c->add(LastminuteSaleschannelPeer::SALESCHANNEL_ID, 1);
        $c->add(LastminuteSaleschannelPeer::PUBLISHED, 1);
        $test = LastminuteSaleschannelPeer::doSelectOne($c);
        if(!$test) continue;
      }
      
      $url = $route->getUrl();

      $ret .= <<<EOT
  <url>
    <loc>http://{$_SERVER['HTTP_HOST']}{$url}</loc>
    <lastmod>{$date}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>\n
EOT;
    }
    
    $ret .= <<<EOT
  </urlset>\n
EOT;

    return $ret;
  }
}
