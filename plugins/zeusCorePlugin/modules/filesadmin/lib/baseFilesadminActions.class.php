<?php

class baseFilesadminActions extends zeusActions
{
  protected $model = 'File';
  
  public function executeIndex(sfWebRequest $request)
  {

  }
  
  public function executeUpload(sfWebRequest $request)
  {

  	ob_start();
	  $this->getUser()->setAttribute('new_file', $_FILES);
  
	  $destination = sfConfig::get('sf_upload_dir').'/'.$_FILES['Filedata']['name'];
	  
	  if (file_exists($destination)) {
	  	// rename file
	  	$c = 1;
	  	$unique = false;
	  	$parts = explode('.', $destination);
	  	$ext = array_pop($parts);
	  	$base = implode('.', $parts);
	  	
	  	while (!$unique && $c < 20) {
	  		$try = $base.'-'.$c.'.'.$ext;
	  		
	  		echo $try."\n";
	  		if (!file_exists($try)) {
	  			$destination = $try;
	  			$unique = true;
	  		}
	  		$c++;
	  	}
	  }
	  
	  move_uploaded_file($_FILES['Filedata']['tmp_name'], $destination);

  	return sfView::NONE;
  
  }
  
  public function executeUpdate(sfWebRequest $request, $customConfig = false)
  {
    $this->setTemplate(sfConfig::get('sf_plugins_dir').'/zeusCorePlugin/modules/filesadmin/templates/update');
  }
  
  public function executeUpdatefile(sfWebRequest $request)
  {
    $files = $this->getUser()->getAttribute('new_file');
    echo $files['Filedata']['name'];
    exit;
  }
  
  public function executeDelete(sfWebRequest $request)
  {
  	$files = new DirectoryIterator(sfConfig::get('sf_upload_dir'));

    $requested = explode('_____', $this->getRequestParameter('filename'));

    foreach ($files as $file) {
      if ($file->isFile()) {
      	if (in_array($file->getFilename(), $requested)) {
      		unlink(sfConfig::get('sf_upload_dir').'/'.$file->getFilename());
      	}
      }
    }
    
    return sfView::NONE;
  }
  
  public function executeBrowser()
  {
    
  }
}