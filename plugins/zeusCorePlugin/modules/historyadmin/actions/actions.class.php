<?php

class historyadminActions extends sfActions 
{
  public function executeDiff()
  {
    $version = VersionPeer::retrieveByPk($this->getRequestParameter('version'));
    
    $this->forward404Unless($version);
    
    $model = $version->getObject();
    $modelpeer = $model.'Peer';
    
    $object = call_user_func_array(array($modelpeer, 'retrieveByPk'), array($version->getObjectId()));
    if (!$object) {
      echo '<p>Kon het object niet laden om verschillen te bepalen.</p>';
      exit;
    }
    
    $object->setCulture('nl_NL');
    
    
    $requested_version = $version->getVersion();
    
    $this->object = $object;
    $this->requested_version = $requested_version;
    
    $config = zeusYaml::load('admin.yml', $this->getRequestParameter('mmodule'));

    $fields = array();
    $analyzer = array();
    
    $values1 = array();
    $values2 = array();
    
    foreach ($config['edit']['fields'] as $key => $field) {
      if (in_array($field['type'], array('input', 'textarea', 'text', 'rich'))) {
        $fields['get'.ucfirst($key)] = $field['label'];
        
        if (in_array($field['type'], array('textarea', 'text', 'rich'))) {
          $analyzer[] = 'get'.ucfirst($key);
        }
      }
    }
    
    $this->fields = $fields;
    
    foreach ($fields as $getter => $label) {
      $v = $object->$getter();
      $v = str_replace('<br>', "\n", $v);
      $v = str_replace('<br />', "\n", $v);
      $v = str_replace('</p>', "\n", $v);
      $v = str_replace('<p>', "", $v);
      $values1[$getter] = strip_tags($v);
    }
    
    $object->loadVersion($requested_version);
    
    foreach ($fields as $getter => $label) {
      $v = $object->$getter();
      $v = str_replace('<br>', "\n", $v);
      $v = str_replace('<br />', "\n", $v);
      $v = str_replace('</p>', "\n", $v);
      $v = str_replace('<p>', "", $v);
      $values2[$getter] = strip_tags($v);
    }
    
    $this->values1 = $values1;
    $this->values2 = $values2;
    
    $this->analyzers = $analyzer;
  }
  
  public function executeRevert()
  {
    $version = VersionPeer::retrieveByPk($this->getRequestParameter('version'));
    
    $this->forward404Unless($version);
    
    $model = $version->getObject();
    $modelpeer = $model.'Peer';
    
    $object = call_user_func_array(array($modelpeer, 'retrieveByPk'), array($version->getObjectId()));
    
    if (!$object) {
      $con = Propel::getConnection();
      $sql = 'insert into '.strtolower($model).' ( id ) values ( '.$version->getObjectId().')';
       
      $statement = $con->prepare($sql);            
 
      $rs = $statement->execute();  

      $object = call_user_func_array(array($modelpeer, 'retrieveByPk'), array($version->getObjectId()));
    }
    
    $object->setCulture('nl_NL');
    $object->save();
    
    $requested_version = $version->getVersion();
    
    $object->revertVersion($requested_version);

    echo 'Het object is hersteld naar de geselecteerde versie.';
    exit;
  }
}