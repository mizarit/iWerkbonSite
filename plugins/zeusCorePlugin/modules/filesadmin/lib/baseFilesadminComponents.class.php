<?php

class baseFilesadminComponents extends sfComponents
{
	public function executeFiles()
	{
		$files = new DirectoryIterator(sfConfig::get('sf_upload_dir'));
    
    $image_mimetypes = array(
      'png', 'jpg', 'jpeg', 'bmp', 'gif'
    );
    
    $filelist = array();
    
    $dummy_time = 0;
    
    foreach ($files as $file) {
      if ($file->isFile()) {
      	$parts = explode('.', $file->getFilename());
      	$ext = strtolower(array_pop($parts));
      	
      	if (in_array($ext, $image_mimetypes)) {
      		$link = ' href="/uploads/'.$file->getFilename().'" class="lightwindow"';
      		$icon = zeusImages::getPresentation('/uploads/'.$file->getFilename(), array('width' => 80, 'height' => 60, 'resize_method' => zeusImages::RESIZE_SCALE));
      		//$icon = zeusImages::resize_image(sfConfig::get('sf_web_dir').'/uploads/'.$file->getFilename(), sfConfig::get('sf_web_dir').'/uploads/thumbs/'.$file->getFilename().'-thumb.jpg', array('width' => 80, 'height' => 60, 'resize_method' => zeusImages::RESIZE_SCALE));
      		
      		//$icon = '/uploads/thumbs/'.$file->getFilename().'-thumb.jpg';
      		//$icon = '/zeusCore/img/mimetypes/ppt.png';
      	}
      	else {
      		$link = ' href="/uploads/'.$file->getFilename().'" target="_blank"';
    
      		$icon = file_exists(sfConfig::get('sf_web_dir').'/zeusCore/img/mimetypes/'.$ext.'.png') ? $ext.'.png' : 'txt.png';
      		$icon = '/zeusCore/img/mimetypes/'.$icon;
      	}
      	
      	if (strstr($_SERVER['HTTP_HOST'], 'cms.')) {
      	  $icon = 'http://www.'.substr($_SERVER['HTTP_HOST'], 4).$icon;
      	}
      	
      	$created = filectime(sfConfig::get('sf_upload_dir').'/'.$file->getFilename());
      	if (!$created) {
      	  $created = time().$dummy_time;
      	}
      	
      	$dummy_time++;
      	
      	$filelist[$created.'-'.$dummy_time] = array(
      	  'name' => basename($file->getFilename()),
      	  'lastmod' => filemtime(sfConfig::get('sf_upload_dir').'/'.$file->getFilename()),
      	  'created' => $created,
      	  'url' => $icon,
      	  'size' => filesize(sfConfig::get('sf_upload_dir').'/'.$file->getFilename())
      	);
      }
    }
    
    
    krsort($filelist);
    

    $filelist = array_values($filelist);
    
    
    
    echo json_encode(array('images' => $filelist));
    return sfView::NONE;
	}
}
