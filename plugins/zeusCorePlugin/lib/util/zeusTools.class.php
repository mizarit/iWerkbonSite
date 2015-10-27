<?php

class zeusTools
{
  public static function getDomainName()
  {
    $domain = 'http://'.sfConfig::get('app_domain');
    return $domain;
  }
  
  public static function dump($params)
  {
    echo '<pre>';
    var_dump($params);
    echo '</pre>';
  }
  
  public static function addthisButtons($config = array())
  {
    $base_config = array(
      'url' => '',
      'description' => '',
      'title' => ''
    );
    
    foreach ($config as $key => $value) {
      $base_config[$key] = $value;
    }

    $base_config['url'] = 'http://'.$_SERVER['HTTP_HOST'].str_replace('http://'.$_SERVER['HTTP_HOST'], '', $base_config['url']);


    $url = urlencode($base_config['url']);
    $title = urlencode($base_config['title']);

    $subject = $base_config['title'];
   

    $body = $base_config['url'];
    ob_start();

    ?>
    <ul class="social-icons">
      <li><img src="/img/soc-twitter.png" title="<?php echo __('Deel dit op Twitter'); ?>" alt="<?php echo __('Deel dit op Twitter'); ?>" style="cursor:pointer;" onclick="window.open('http://twitter.com/intent/tweet?text=<?php echo $title; ?>&amp;url=<?php echo $url; ?>&amp;via=PlekjeVrij', 'share', 'menubar=0,resizable=1,status=0,width=450,height=400');"></li>
      <li><img src="/img/soc-facebook.png" title="<?php echo __('Deel dit op Facebook'); ?>" alt="<?php echo __('Deel dit op Facebook'); ?>" style="cursor:pointer;" onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo $url; ?>', 'share', 'menubar=0,resizable=1,status=0,width=750,height=330');"></li>
      <li><img src="/img/soc-hyves.png" title="<?php echo __('Deel dit op Hyves'); ?>" alt="<?php echo __('Deel dit op Hyves'); ?>" style="cursor:pointer;" onclick="window.open('http://www.hyves.nl/profilemanage/add/tips/?name=<?php echo $title; ?>&amp;text=<?php echo $url; ?>', 'share', 'menubar=0,resizable=1,scrollbars=1,status=0,width=550,height=550');"></li>
      <li><img src="/img/soc-mail.png" title="<?php echo __('E-mail dit naar een vriend'); ?>" alt="<?php echo __('E-mail dit naar een vriend'); ?>" style="cursor:pointer;" onclick="window.location.href='mailto:ontvanger@voorbeeld.nl?subject=<?php echo $subject; ?>&amp;body=<?php echo $body; ?>';"></li>
    </ul>
<?php

    return ob_get_clean();
 
  }
  
  public static function humanReadableFilesize($bytes) {
    $i=0;
    $iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
    
    while (($bytes/1024)>1) {
      $bytes = $bytes/1024;
      $i++;
    }
    
    return str_replace('.', ',', substr($bytes,0,strpos($bytes,'.')+2).$iec[$i]);
  }
  
  public static function smartText($string, $length)
  {
    $words = explode(' ', $string);
    $out = '';
    foreach ($words as $word)
    {
      if (strlen($out)+strlen($word)+1 < $length) {
        $out .= $word . ' ';
      }
      else {
        $out = trim($out).'...';
        break;
      }
    }
    
    
    return trim($out, ' ');
  }
  
  public static function smartUrl($string)
  {
    $chars = array(
		// Decompositions for Latin-1 Supplement
  		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
  		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
  		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
  		chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
  		chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
  		chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
  		chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
  		chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
  		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
  		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
  		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
  		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
  		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
  		chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
  		chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
  		chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
  		chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
  		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
  		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
  		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
  		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
  		chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
  		chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
  		chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
  		chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
  		chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
  		chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
  		chr(195).chr(191) => 'y',
  		// Decompositions for Latin Extended-A
  		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
  		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
  		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
  		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
  		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
  		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
  		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
  		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
  		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
  		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
  		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
  		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
  		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
  		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
  		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
  		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
  		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
  		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
  		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
  		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
  		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
  		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
  		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
  		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
  		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
  		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
  		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
  		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
  		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
  		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
  		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
  		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
  		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
  		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
  		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
  		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
  		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
  		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
  		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
  		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
  		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
  		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
  		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
  		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
  		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
  		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
  		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
  		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
  		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
  		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
  		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
  		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
  		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
  		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
  		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
  		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
  		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
  		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
  		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
  		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
  		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
  		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
  		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
  		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
  		// Euro Sign
  		chr(226).chr(130).chr(172) => 'E',
  		// GBP (Pound) Sign
  		chr(194).chr(163) => '',
  		'.' => '',
  		'?' => '',
  		'"' => '',
  		"'" => '',
  		'!' => '',
  		':' => '',
  		'/' => '-',
  		',' => '',
  		'(' => '',
  		')' => ''
  	);
  
  	$string = strtr($string, $chars);
    $string = rawurlencode($string);
    if(strpos($string, '[')) {
      $string = substr($string,0, strpos($string,'['));
    }
  	return strtolower(str_replace(array(' ', '%20', '%'), '-', trim($string)));
  }
  
  public static function checkLicense()
  {
    return true;
    static $checked = false;
    if (!$checked) {
      $checked = true;
      
      $site = str_replace('www.', '', str_replace('cms.', '', $_SERVER['HTTP_HOST']));
      $hash = md5($site);
      
      $file = file_get_contents(sfConfig::get('sf_root_dir').'/lib/vendor/symfony/license.dat');
      $config = unserialize($file);
      if (!isset($config[$hash]['hash'])) {
        die('Unlicensed zeusCMS');
      }
      
      $periodic = @unserialize(@file_get_contents(sfConfig::get('sf_log_dir').'/zeus_cms.log'));
      $notify = false;
      if (!$periodic) {
        $notify = true;
      }
      elseif (!isset($periodic[$_SERVER['HTTP_HOST']]) || !isset($periodic[$_SERVER['HTTP_HOST']]['time']))
      {
        $notify = true;
      }
      elseif ($periodic[$_SERVER['HTTP_HOST']]['time'] < (strtotime('-1 month')))
      {
        $notify = true;
      }
      
      if ($notify) {
        $periodic[$_SERVER['HTTP_HOST']]['time'] = time();
        $periodic[$_SERVER['HTTP_HOST']]['ip'] = $_SERVER['REMOTE_ADDR'];
        $time = date('d-m-Y H:i:s', time() + 3600);
        mail('ricardo.matters@mizar-it.nl', 'Usage ping for '.$_SERVER['HTTP_HOST'], "
domain: {$_SERVER['HTTP_HOST']} 
ip:     {$_SERVER['REMOTE_ADDR']}
time:   {$time}  
        ", "From: zeusCMS <website@{$_SERVER['HTTP_HOST']}>\r\n" .
          "Reply-To: info@mizar-it.nl\r\n" .
          "X-Mailer: PHP/" . phpversion());
        
        file_put_contents(sfConfig::get('sf_log_dir').'/zeus_cms.log', serialize($periodic));
      }
    }
  }
}