<?php

class zeusCoordinates
{
  public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    $distance = (3958*3.1415926*sqrt(($lat2-$lat1)*($lat2-$lat1) + cos($lat2/57.29578)*cos($lat1/57.29578)*($lon2-$lon1)*($lon2-$lon1))/180);
    
    return $distance;
  }
  
  public static function getCoordinates($address)
  {
    $location = json_decode((file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='.urlencode($address))));
    $lon = $lat = null;
    if($location->status == 'OK') {
      $lat = $location->results[0]->geometry->location->lat;
      $lon = $location->results[0]->geometry->location->lng;
    }
    return array('longitude' => $lon, 'latitude' => $lat);
  /*
    $api_key = zeusGoogleMaps::getApiKey();

    $q = urlencode($address);

    $address = "http://maps.google.com/maps/geo?q={$q}&output=xml&key={$api_key}";
    

    // Retrieve the URL contents
    $page = @file_get_contents($address);
    
    if (!$page) {
      // great! we've got a 403 throttle problem :(
      return array('longitude' => null, 'latitude' => null); 
    }

    // Parse the returned XML file
    $xml = new SimpleXMLElement($page);
    
    if ($xml && is_object($xml)) {
      if ($xml->Response) {
        if ($xml->Response->Placemark) {
          if ($xml->Response->Placemark->Point) {
            if ($xml->Response->Placemark->Point->coordinates) {
              // Parse the coordinate string
              $parts = explode(',',  $xml->Response->Placemark->Point->coordinates);
              
              if (count($parts) == 3) {
                list($longitude, $latitude, $altitude) = $parts;
                return array('longitude' => $longitude, 'latitude' => $latitude);
              }
            }
          }
        }
      }
    }
 
    
    return array('longitude' => null, 'latitude' => null); 
    */
  }
}