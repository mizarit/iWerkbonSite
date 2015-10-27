<?php
/**
 * ZeusCMS
 *
 * zeusImages
 *
 * @author Ricardo Matters <ricardo.matters@mizar-it.nl>
 * @copyright Mizar IT
 * @since 2009
 *
 */

class zeusImages
{
  const LOCATION_PATH = '/img/';

  /**
   * De afbeelding wordt proportioneel verkleind om binnen bepaalde afmetingen te passen
   */
  const RESIZE_SCALE   = 1;

  /**
   * De afbeelding wordt proportioneel verkleind en vervolgens vanuit het midden afgeknipt om precies binnen bepaalde afmetingen te passen
   */
  const RESIZE_CHOP    = 2;

  /**
   * De afbeelding wordt onafhankelijk van zijn afmetingen geschaald naar bepaalde afmetingen
   */
  const RESIZE_STRETCH = 3;


  public static function showImages($object = false, $element = false, $key = 1, $from_ajax = false) {

    if (!$from_ajax) {
      sfContext::getInstance()->getUser()->setAttribute('imageobject', $object);
      sfContext::getInstance()->getUser()->setAttribute('imageelement', $element);

      if (!is_dir(getcwd().self::LOCATION_PATH.strtolower(get_class($object)))) {
        mkdir(getcwd().self::LOCATION_PATH.strtolower(get_class($object)), 0777);
      }

      if (!is_dir(getcwd().self::LOCATION_PATH.strtolower(get_class($object)).'/'.$object->getId())) {
        mkdir(getcwd().self::LOCATION_PATH.strtolower(get_class($object)).'/'.$object->getId(), 0777);
      }

      $_SESSION['imagemanagerpath'] = getcwd();
      $_SESSION['imagemanagerurl'] = '/img';
      $_SESSION['imagemanagerdirectory'] = strtolower(get_class($object));
      $_SESSION['imagemanagerobjectid'] = $object->getId();
      $_SESSION['imagemanagerobject'] = get_class($object);

      // cleanup
      $c = new Criteria();

      $class = get_class($object);
      $class_name = strtoupper($class);

      $c->clear();

      $c->add(constant("{$class}ImagePeer::{$class_name}_ID"), Criteria::ISNULL);
      $images = call_user_func(array("{$class}ImagePeer", 'doSelect'), $c);

      if ($images) {
        foreach ($images as $image) {
          // fixme:
          // om onbekende reden werkt deze query niet juist
          // hier moeten alle eventuele images zonder gekoppeld object verwijderd worden om de directory schoon te houden

          //echo $image->getImage()->getImage();
        }
      }

      unset($c);

    }
    else {
      $object = sfContext::getInstance()->getUser()->getAttribute('imageobject');
      $element = sfContext::getInstance()->getUser()->getAttribute('imageelement');
    }

    echo <<<EOT
          <fieldset>
            <legend>Image upload</legend>\n
EOT;

    use_helper('Images');
    images_tag($object, array());
    echo <<<EOT
          </fieldset>
EOT;

  }

  static public function get_input_tag($name, $object, $options = array()) {
    ob_start();
    msImages::input_tag($name, $object, $options);
    return ob_get_clean();
  }

  static public function input_tag($name, $object, $options = array(), $to_buffer = false)
  {
    if (!isset($options['width'])) $options['width'] = 80;
    if (!isset($options['height'])) $options['height'] = 120;

    $ostr = strtolower(get_class($object));

    $koppelobject = get_class($object) . 'ImagePeer';
    $imageobject = get_class($object) . 'Image';

    $koppelveld = strtoupper(get_class($object)) . '_ID';

    $c = new Criteria();
    $c->add(constant("$koppelobject::NAME"), $name);
    $c->add(constant("$koppelobject::$koppelveld"), $object->getId());

    $image = call_user_func(array($koppelobject, 'doSelectOne'), $c);

    $ret = <<<EOT
<input type="file" name="image_{$ostr}_{$name}" id="image_{$ostr}_{$name}">\n
EOT;

    if($image instanceof $imageobject) {
      if(file_exists(sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $image->getImage()->getDirectory() . '/' . $image->getImage()->getImage())) {
        $fn = str_replace(' ', '%20', $image->getImage()->getImage());
        $ret .= "<div class=\"tabbed\">\n";
        $url = url_for('/images?delete='.$image->getId().'&object='.$ostr);

        $img = self::getPresentation($image->getImage(), $options);

        $ret .= <<<EOT
  <div style="background: #fff; border: 1px solid #c0c0c0; padding: 2px; position:relative; width:{$options['width']}px; height:{$options['height']}" id="preview-{$ostr}-{$name}">
    <img src="$img" alt="">
    <a style="position:absolute;top:1px;right:1px;" href="#" onclick="if (confirm('Weet je het zeker?')) { new Ajax.Request('{$url}', {asynchronous:true, evalScripts:true, onComplete: function(request){ $('preview-{$ostr}-{$name}').style.display = 'none'; $('preview-{$ostr}-{$name}').style.display = 'none'; } } ); } return false;">
    <img id="delete-{$ostr}-{$name}" src="/backend/img/icons/cross.gif" alt=""></a>
  </div>
</div>\n
EOT;
      }
    }

    if ($to_buffer) {
      return $ret;
    }

    echo $ret;
  }

  static public function input_tag_save($name, $object, $options = array()) {
    $ostr = strtolower(get_class($object));

    if (isset($_FILES["image_{$ostr}_{$name}"]) && $_FILES["image_{$ostr}_{$name}"]['error'] == 0)
    {
      //if ($image = $object->getImageByName($name)) $image->delete();
      // dit moet anders, want een image is gekoppeld en nooit direct te verwijderen!
      if ($image = $object->getImageByName($name)) {
        $object->deleteImageByName($name);
      }

      if (!is_dir(sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $ostr ))
        mkdir(sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $ostr, 0755);

      if (!is_dir(sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $ostr .'/'. $object->getId()))
        mkdir(sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $ostr .'/' . $object->getId(), 0755);

      // afbeelding opslaan
      $outfile = sfConfig::get('sf_web_dir') . self::LOCATION_PATH . $ostr . '/' . $object->getId() .'/'. $_FILES["image_{$ostr}_$name"]['name'];
      //$outfile = str_replace(sfConfig::get('sf_root_dir'), '', $outfile);
      //die($outfile);
      $filename = self::resize_image($_FILES["image_{$ostr}_$name"]['tmp_name'], $outfile, array('quality' => 100));

      // afbeelding toevoegen in de database
      $c = new Criteria();
      $c->add(ImagePeer::IMAGE, $filename);
      $c->add(ImagePeer::DIRECTORY, "$ostr/". $object->getId());
      $image_pk = ImagePeer::doInsert($c);

      // afbeelding aan object koppelen in de database
      $c->clear();
      $koppelobject = get_class($object) . 'ImagePeer';
      $koppelveld = strtoupper(get_class($object)) . '_ID';

      $c->add(constant("$koppelobject::NAME"), $name);
      $c->add(constant("$koppelobject::$koppelveld"), $object->getId());
      call_user_func(array($koppelobject, 'doDelete'), $c);

      $c->clear();
      $c->add(constant("$koppelobject::NAME"), $name);
      $c->add(constant("$koppelobject::IMAGE_ID"), $image_pk);
      $c->add(constant("$koppelobject::$koppelveld"), $object->getId());
      call_user_func(array($koppelobject, 'doInsert'), $c);

      // als er al instellingen worden meegegeven voor de afmetingen van de afbeelding kan die versie meteen worden aangemaakt
      if (count($options)) {
        //self::getPresentation($object->getImageByName($name), $options);
      }
    }
  }

  static public function resize_image($infile, $outfile, $options = array())
  {
    
    // een paar standaard instellingen verzamelen
    if (!isset($options['width']))
      if (isset($options['height'])) {
        $options['width'] = 2000;
        $options['resize_method'] = self::RESIZE_SCALE;
      }
      else {
        $options['width'] = 1024;
      }

    if (!isset($options['height']))
      if (isset($options['width'])) {
        $options['height'] = 2000;
        $options['resize_method'] = self::RESIZE_SCALE;
      }
      else {
        $options['height'] = 768;
      }

    if (!isset($options['resize_method'])) {
      $options['resize_method'] = self::RESIZE_SCALE;
    }

    if (!isset($options['enlarge'])) {
      $options['enlarge'] = false;
    }

    if (!isset($options['grayscale'])) {
      $options['grayscale'] = false;
    }

    if (!isset($options['quality'])) {
      $options['quality'] = 90;
    }

    if (!file_exists($infile) || is_dir($infile) || !is_readable($infile))
      return false;

    $image = @imagecreatefromstring(@file_get_contents($infile));
      
    if (!$image) {
      return false;
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    $type = IMAGETYPE_JPEG;
    //list($width, $height, $type) = getimagesize($infile);


    $handler[IMAGETYPE_JPEG]  = 'imagecreatefromjpeg';
    $handler[IMAGETYPE_GIF]  = 'imagecreatefromgif';
    $handler[IMAGETYPE_PNG]  = 'imagecreatefrompng';

    if (!isset($handler[$type]))
      return false;

    $function = $handler[$type];

    if (($width == $options['width'] && $height == $options['height']) || ($width < $options['width'] && $height < $options['height'] && !$options['enlarge'] && !$options['grayscale'])) {
      // de afbeelding is kleiner dan de opgegeven maximum breedte & hoogte en mag niet worden vergroot, dus alleen kopieren, niet resizen
      
      if(!copy($infile, $outfile)) {
        throw new sfException("Can't copy from {$infile} to {$outfile} !");
      }
      chmod($outfile, 0777);
      return basename($outfile);
    }
    else {
      static $headercount = 1;

      $ratio_orig = $width / $height;
      $ratio_new = $options['width'] / $options['height'];

      switch ($options['resize_method'])
      {
        case self::RESIZE_SCALE:
          if ($ratio_new > $ratio_orig) {
            // de afbeelding is proportioneel te breed
            $maxheight = $options['height'];
            $maxwidth = $options['height'] * $ratio_orig;
          }
          else {
            // de afbeelding is proportioneel te hoog
            $maxwidth = $options['width'];
            $maxheight = $options['width'] / $ratio_orig;
          }

          $image_p = imagecreatetruecolor($maxwidth, $maxheight);
          //$image = $function($infile);
          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $maxwidth, $maxheight, $width, $height);

          break;
        case self::RESIZE_STRETCH:
          $maxwidth = $options['width'];
          $maxheight = $options['height'];

          $image_p = imagecreatetruecolor($maxwidth, $maxheight);
          //$image = $function($infile);
          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $maxwidth, $maxheight, $width, $height);

          break;
        case self::RESIZE_CHOP:
          //$image = $function($infile);

          if ($ratio_new < $ratio_orig) {
            // de afbeelding is proportioneel te breed

            // afmetingen van de tijdelijke afbeelding
            if ($options['enlarge']) {
              // hoogte vastzetten op de gevraagde hoogte
              $tempheight = $options['height'];

              // breedte proportioneel meeschalen
              $tempwidth = $tempheight * $ratio_orig;

              // het resultaat wordt altijd een afbeelding op het gevraagde formaat
              $maxheight = $tempheight;
              $maxwidth = $options['width'];
            }
            else {
              // hoogte vastzetten op de gevraagde hoogte, of de hoogte van de afbeelding mocht die kleiner zijn
              $tempheight = min($options['height'], $height);

              // breedte proportioneel meeschalen
              $tempwidth = $tempheight * $ratio_orig;

              // de hoogte van het resultaat is de gevraagde hoogte, of de hoogte van de afbeelding mocht die kleiner zijn
              $maxheight = $tempheight;

              // de breedte van het resultaat is de gevraagde breedte, of de breedte van de afbeelding mocht die kleiner zijn
              $maxwidth = min($options['width'], $tempwidth);
            }

            $cut_x = ($tempwidth - $options['width']) / 2;
            $cut_y = 0;
          }
          else {
            // de afbeelding is proportioneel te hoog

            // afmetingen van de tijdelijke afbeelding
            if ($options['enlarge']) {
              // breedte vastzetten op de gevraagde breedte
              $tempwidth = $options['width'];

              // hoogte proportioneel meeschalen
              $tempheight = $tempwidth / $ratio_orig;

              // het resultaat wordt altijd een afbeelding op het gevraagde formaat
              $maxwidth = $tempwidth;
              $maxheight = $options['height'];
            }
            else {
              // breedte vastzetten op de gevraagd breedte, of de breedte van de afbeelding mocht die kleiner zijn
              $tempwidth = min($options['width'], $width);

              // hoogte proportioneel meeschalen
              $tempheight = $tempwidth / $ratio_orig;

              // de breedte van het resultaat is de gevraagde breedte, of de breedte van de afbeelding mocht die kleiner zijn
              $maxwidth = $tempwidth;

              // de hoogte van het resultaat is de gevraagde hoogte, of de hoogte van de afbeelding mocht die kleiner zijn
              $maxheight = min($options['height'], $tempheight);
            }

            $cut_x = 0;
            $cut_y = ($tempheight - $maxheight) / 2;
          }

          $image_t = imagecreatetruecolor($tempwidth, $tempheight);
          imagecopyresampled($image_t, $image, 0, 0, 0, 0, $tempwidth, $tempheight, $width, $height);

          // afmetingen van de definitieve afbeelding
          $image_p = imagecreatetruecolor($maxwidth, $maxheight);

          imagecopy($image_p, $image_t, 0, 0, $cut_x, $cut_y, $maxwidth, $maxheight);
      }
    }

    if ($options['grayscale']) {
      $x = imagesx($image_p);
      $y = imagesy($image_p);
      for($i=0; $i<$y; $i++)
      {
        for($j=0; $j<$x; $j++)
        {
          $pos = imagecolorat($image_p, $j, $i);
          $f = imagecolorsforindex($image_p, $pos);
          $gst = $f["red"]*0.15 + $f["green"]*0.5 + $f["blue"]*0.35;
          $col = imagecolorresolve($image_p, $gst, $gst, $gst);
          imagesetpixel($image_p, $j, $i, $col);
        }
      }
    }

    if (file_exists($outfile)) @unlink($outfile);

    if (@imagejpeg($image_p, $outfile, $options['quality'])) {
      @chmod($outfile, 0777);
      return basename($outfile);
    }
    else {
      return false;
    }
  }

  static public function getPath($object, $options = array())
  {
    if ($object instanceof Image) {
      $url =  self::LOCATION_PATH . $object->getDirectory().'/'.$object->getImage();
    }
    else {
      $url = $object;
    }

    ksort($options);
    $hash = substr(md5(serialize($options)),0,8);

    $ar = explode('/', $url);

    $filename = $ar[count($ar)-1];

    unset($ar[count($ar)-1]);
    $cpath = implode('/', $ar);

    $path = sfConfig::get('sf_web_dir') . $cpath;

    if (is_file("$path/$hash/$filename")) {
      return "$path/$hash/$filename";
    }
    else {
      return null;
    }
  }

  static public function getPresentation($object, $options = array())
  {
    if ($object instanceof Image) {
      $url = self::LOCATION_PATH.$object->getDirectory().'/'.$object->getImage();
    }
    elseif (is_object($object)) {
      $object = $object->getImage();
      $url = self::LOCATION_PATH.$object->getDirectory().'/'.$object->getImage();
    } else {
      $url = $object;
    }

    ksort($options);
    $hash = substr(md5(serialize($options)),0,8);

    $ar = explode('/', $url);

    $filename = $ar[count($ar)-1];

    unset($ar[count($ar)-1]);
    $cpath = implode('/', $ar);

    if (!$cpath) {
      return $object;
    }

    $rootDir = isset($options['root']) ? $options['root'] :  sfConfig::get('sf_web_dir');
    $path = $rootDir . $cpath;

    if (!is_dir("$path/$hash")) {
      mkdir("$path/$hash", 0777, true);
      //echo "$path/$hash";
    }

    if (!is_file("$path/$hash/$filename")) {
      self::resize_image($rootDir ."/$url", "$path/$hash/$filename", $options);
    }
    
    return "$cpath/$hash/$filename";
  }

}

