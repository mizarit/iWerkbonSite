<?php 

class zeusTiny
{
  public static function parse_contents($contents)
  {
    $contents = html_entity_decode($contents); 
    
    preg_match_all('/<img(.+?)\/>/si', $contents, $ar);
    foreach ($ar[1] as $match) {
      $parts = array();
      preg_match_all('/([a-z]+?)="(.+?)"/', $match, $attributes);
      foreach ($attributes[1] as $key => $attr) {
        $parts[$attr] = $attributes[2][$key];
      }
      
      if (isset($parts['width']) && isset($parts['height'])) {
        $filename = zeusImages::getPresentation($parts['src'], array('width' => $parts['width'], 'height' => $parts['height']));
        $contents = str_replace($parts['src'], $filename, $contents);
      }
    }
    
    $contents = str_replace(' border="0"', '', $contents);
    $contents = str_replace(' target="_blank"', ' rel="external"', $contents);
    
    preg_match_all('/<object(.+?)\/object>/si', $contents, $ar);
    
    foreach ($ar[1] as $key_item => $match) {
      preg_match('/data="(.+?).flv"/si', $match, $ar2);
      $parts = array();
      preg_match_all('/([a-z]+?)="(.+?)"/', $match, $attributes);
      foreach ($attributes[1] as $key => $attr) {
        $parts[$attr] = $attributes[2][$key];
      }

      if (isset($parts['data'])) {
        $filename = $parts['data'];
        $width = isset($parts['width']) ? $parts['width'] : 400;
        $height = isset($parts['height']) ? $parts['height'] : 315;
        
        $fileparts = explode('.', $filename);
        array_pop($fileparts);
        $fileparts[] = 'jpg';
        $preview = implode('.', $fileparts);
        ob_start();
        ?>
<script type="text/javascript">
document.write('<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="<?php echo $width; ?>" height="<?php echo $height; ?>">');
document.write('<param name="movie" value="/zeusCore/js/mediaplayer-viral/player-viral.swf" />');
document.write('<param name="allowfullscreen" value="true" />');
document.write('<param name="allowscriptaccess" value="always" />');
document.write('<param name="flashvars" value="autostart=true&file=<?php echo $filename ?>&image=<?php echo $preview ?>" />');
document.write('<embed type="application/x-shockwave-flash" id="player2" name="player2" src="/zeusCore/js/mediaplayer-viral/player-viral.swf" width="<?php echo $width; ?>" height="<?php echo $height; ?>" allowscriptaccess="always" allowfullscreen="true" flashvars="controlbar=none&icons=false&autostart=true&file=<?php echo $filename ?>&image=<?php echo $preview ?>" /><\/object>');
</script>
	<?php
	       $player = ob_get_clean();
	       $contents = str_replace($ar[0][$key_item], $player, $contents);
      }
    }// 491  279
    
    return $contents;
  }
}