<?php

class zeusGoogleMaps
{
  private static $api_key;
  
  public static function getApiKey()
  {
    if (zeusGoogleMaps::$api_key) {
      return zeusGoogleMaps::$api_key;
    }
    
    $api_key = false;
    
    $api_keys = sfConfig::get('app_google_maps');
    foreach ($api_keys as $key) {
      if ($key['domain'] == $_SERVER['HTTP_HOST']) {
        $api_key = $key['key'];
      }
    }
    
    if (!$api_key) {
      echo $_SERVER['HTTP_HOST'];
      var_dump($api_keys);
      die ('No API key for this domain');
    }
    
    zeusGoogleMaps::$api_key = $api_key;
    return $api_key;
  }
  
  public static function getMap($object, $config = array('width' => 300, 'height' => 300, 'range' => 5))
  {
    // determine center of map
    if (is_array($object) && isset($object[0])) {
      $longitude = 0;
      $latitude = 0;
      
      $longitudes = $latitudes = array();
      
      $cnt = 0;
      foreach ($object as $loc) {
        if ($loc->getLatitude() > 0) {
          $latitude += $loc->getLatitude();
          $longitude += $loc->getLongitude();
          $latitudes[] = $loc->getLatitude();
          $longitudes[] = $loc->getLongitude();
          $cnt++;
        }
      }
     
      if ($cnt > 0) {
        $latitude = array_sum($latitudes) / $cnt;
        $longitude = array_sum($longitudes) / $cnt;
      }
      
      $objects = $object;
      
      $range = zeusCoordinates::calculateDistance(max($longitudes), max($latitudes), min($longitudes), min($latitudes));
      
      $zoom_map[0] = 8;
      $zoom_map[5] = 11;
      $zoom_map[10] = 10;
      $zoom_map[20] = 9.5;
      $zoom_map[30] = 9;
      $zoom_map[40] = 8.5;
      $zoom_map[50] = 8;
      
      $last = -1;
      $zoom = 5;
      
      foreach ($zoom_map as $factor_key => $factor) {
        if ($range > $last && $range < $factor_key) {
          //var_dump(array($range, $last, $factor_key)) ;
          $zoom = $factor;
        }
        $last = $factor_key;
      }
      
      if ($zoom == 5) {
        // one plekje, is it in Nederland?
        if ($longitude > 4 && $longitude < 6) {
          if ($latitude > 50 && $latitude < 53) {
            $zoom = 9;
          }
        }
      }
    }
    elseif (is_object($object)) {
      $longitude = $object->getLongitude();
      $latitude = $object->getLatitude();
      
      $objects = array($object);
      
      $zoom = 11;
    }
    else {
      $longitude = $object['longitude'];
      $latitude = $object['latitude'];
      
      $zoom = isset($config['zoom']) ? $config['zoom'] : 15;
      $objects = array($object);
    }
    
    if (isset($config['zoom'])) {
      $zoom = $config['zoom'];
    }
    
    if (isset($config['center'])) {
      $longitude = $config['center']['longitude'];
      $latitude = $config['center']['latitude'];
    }
    
    // max the number of objects on the map
    //$objects = array_slice($objects, 0, 20);
    
    $api_key = zeusGoogleMaps::getAPiKey();
    
    ob_start();
    // <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $api_key ? >" type="text/javascript"></script>

    ?>
<script type="text/javascript">

var map = null;

<?php if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest()) { ?>
$(document).ready(function () {
<?php } ?>

  if (GBrowserIsCompatible())
  {
    map = new GMap2($('map'));
    map.setMapType(G_SATELLITE_MAP);
    map.addControl(new GLargeMapControl());
    //map.addControl(new GMapTypeControl());

    point = new GLatLng(<?php echo $latitude ?>, <?php echo $longitude; ?>);
    
    map.setCenter(point, <?php echo $zoom; ?>, G_NORMAL_MAP);
    
    var blueIcon = new GIcon(G_DEFAULT_ICON);
    blueIcon.iconSize = new GSize(32, 32);
    blueIcon.image = "/zeusCore/img/dots/blue-dot.png";
    
    var redIcon = new GIcon(G_DEFAULT_ICON);
    redIcon.iconSize = new GSize(32, 32);
    redIcon.image = "/zeusCore/img/dots/red-dot.png";

    var orangeIcon = new GIcon(G_DEFAULT_ICON);
    orangeIcon.iconSize = new GSize(32, 32);
    orangeIcon.image = "/zeusCore/img/dots/orange-dot.png";

    var purpleIcon = new GIcon(G_DEFAULT_ICON);
    purpleIcon.iconSize = new GSize(32, 32);
    purpleIcon.image = "/zeusCore/img/dots/purple-dot.png";
    
    var yellowIcon = new GIcon(G_DEFAULT_ICON);
    yellowIcon.iconSize = new GSize(32, 32);
    yellowIcon.image = "/zeusCore/img/dots/yellow-dot.png";
    
    var greenIcon = new GIcon(G_DEFAULT_ICON);
    greenIcon.iconSize = new GSize(32, 32);
    greenIcon.image = "/zeusCore/img/dots/green-dot.png";
       
    markerOptions = { icon:blueIcon };
    markerOptions0 = { icon:blueIcon };
    markerOptions1 = { icon:redIcon };
    markerOptions2 = { icon:orangeIcon };
    markerOptions3 = { icon:purpleIcon };
    markerOptions4 = { icon:yellowIcon };
    markerOptions5 = { icon:greenIcon };



<?php 
$c = 0;
foreach ($objects as $object) { 
  $c++;
  $url = '';
  $icon = 'markerOptions0';
  
  if (isset($config['icons']) && isset($config['icons'][$object->getId()])) {
    $icon = 'markerOptions'.$config['icons'][$object->getId()];
  }
  
  if (isset($config['urls']) && isset($config['urls'][$object->getId()])) {
    $url = '<br><a href="'.$config['urls'][$object->getId()]. '">Lees meer over deze locatie</a>';
  }
  
  if (is_object($object)) {
    if($object->getLatitude() > 0) {
  ?>
    point_<?php echo $c; ?> = new GLatLng(<?php echo $object->getLatitude(); ?>, <?php echo $object->getLongitude(); ?>);
    var marker_<?php echo $c; ?> = new GMarker(point_<?php echo $c; ?>, <?php echo $icon; ?>);
    map.addOverlay(marker_<?php echo $c; ?>);
    
    GEvent.addListener(marker_<?php echo $c; ?>, "click", function() {
      marker_<?php echo $c; ?>.openInfoWindowHtml('<strong><?php echo addslashes($object->getTitle()).'<\/strong><br>'.addslashes($object->getAddress()).'<br>'.addslashes($object->getZipcode()).' '.addslashes($object->getCity()); ?><?php echo $url; ?>');
    });
<?php }
  }
  else {
    // arrray ?>
   // point = new GLatLng(<?php echo $object['latitude']; ?>, <?php echo $object['longitude']; ?>);
   // var marker = new GMarker(point);
   // map.addOverlay(marker);
    <?php
  } 
} ?>
  }
  
  Event.observe($('map'), 'scroll', function(e) {
    zoom = e.detail * -40;
    if (zoom == -120) 
    {
      map.zoomOut();
    }
    else if (zoom == 120)
    {
      map.zoomIn();  
    }
  });
  
<?php if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest()) { ?>
});
<?php } ?>
</script>
<div id="map" style="height: <?php echo $config['height']; ?>px;width: <?php echo $config['width']; ?>px;"></div>
<?php

    return ob_get_clean();
  }
  
  public static function getDirections($object, $config = array('width' => 570, 'height' => 300, 'range' => 5))
  {
    //$api_key = zeusGoogleMaps::getAPiKey();
    
    //$user = plekjevrijUser::getUser();
    if (!isset($config['height'])) $config['height'] = 300;
    if (!isset($config['width'])) $config['width'] = 570;
    $address = sfContext::getInstance()->getRequest()->getParameter('address');
    $longitude = sfContext::getInstance()->getRequest()->getParameter('longitude');
    $latitude = sfContext::getInstance()->getRequest()->getParameter('latitude');
    
    if ($longitude > 0) {
      $from = $latitude.','.$longitude;
    }
    else {
      $from = $address;
    }
    if (!isset($config['key'])) {
      $config['key'] = 'AIzaSyDi8_CvzY8OVhMM6z2qiRboLGDHrPI7o_U';
    }
    
    $to = isset($config['to']) ? $config['to'] : 'Utrecht';
    ob_start();
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $config['key']; ?>" type="text/javascript"></script>
<script type="text/javascript">
var map;
var gdir;
var geocoder = null;
var addressMarker;

function Ginitialize() {
  if (GBrowserIsCompatible()) {      
    map = new GMap2(document.getElementById("map_canvas"));
    gdir = new GDirections(map, document.getElementById("directions"));
    GEvent.addListener(gdir, "error", handleErrors);
  }
}

function setDirections(fromAddress, toAddress, locale) {
  gdir.load("from: " + fromAddress + " to: " + toAddress, { "locale": locale });
}

function handleErrors(){
  if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
    alert("Het opgegeven adres kon niet gevonden worden. Dit kan komen doordat het adres nog erg nieuw is en nog niet is toegevoegd aan het systeem, of het adres verkeerd ingevoerd is.\nError code: " + gdir.getStatus().code);
  else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
    alert("A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + gdir.getStatus().code);
  else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
    alert("The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + gdir.getStatus().code);
  else if (gdir.getStatus().code == G_GEO_BAD_KEY)
    alert("The given key is either invalid or does not match the domain for which it was given. \n Error code: " + gdir.getStatus().code);
  else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
    alert("A directions request could not be successfully parsed.\n Error code: " + gdir.getStatus().code);
  else alert("Er is een onbekende fout opgetreden.");
}

Event.observe(window, 'load', function () { 
  Ginitialize();
  setDirections("<?php echo $from ?>", "<?php echo $to; ?>", "nl_NL");
});

Event.observe(window, 'unload', function () { 
  GUnload();
});
</script>
<form action="#" method="post" id="directions-form">
  <fieldset>
    <legend>Routeplanner</legend>
    <div class="form-row">
      <div class="form-label"><label for="address">Adres</label></div>
      <input type="text" name="address" id="address" value="<?php echo $address; ?>"> <button type="submit" class="button-5"><span>Route bepalen</span></button>
    </div>
  </fieldset>
</form>

<h4>Kaart</h4>
<div id="map-container">
  <div id="map_canvas" style="width: <?php echo $config['width']; ?>px; height: <?php echo $config['height']; ?>px"></div>
</div>

<table class="directions">
  <tr>
    <td><h4>Aanwijzingen</h4></td>
  </tr>
  <tr>
    <td><a href="#" onclick="window.print();return false;"><img src="/img/printer.png" alt="Route-beschrijving afdrukken" title="Route-beschrijving afdrukken"></a></td>
  </tr>
  <tr>
    <td style="vertical-align:top;"><div id="directions" style="width: <?php echo $config['width'] - 20; ?>px"></div></td>
  </tr>
</table> 
<?php

    return ob_get_clean();
  }
}