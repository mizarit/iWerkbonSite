<?php

class zeusVersionBehavior
{
  private static $revert = false;
  
  public function preSave(BaseObject $object)
  {   
    $model = get_class($object);
    $getter = 'get'.$model.'I18Ns';

    $modified = $object->isModified();
    
    if (!$modified && method_exists($object, $getter)) {
      foreach ($object->$getter() as $object_i18n) {
        if ($object_i18n->isModified()) {
          $modified = true;
        }
      }
    }
    
    if ($modified) {
      $this->addVersion($object);
    }
  }
  
  public function postSave(BaseObject $object)
  {      

  }
  
  public function postDelete(BaseObject $object)
  {
   
  }
  
  public function preDelete(BaseObject $object)
  {
    $model = get_class($object);
    $model_id = $object->getId();
    $peer = $model.'Peer';
    $i18n_peer = $model.'I18NPeer';
    
    $fields = call_user_func_array(array($peer, 'getFieldNames'), array());

    $user_id = sfContext::getInstance()->getUser()->getAttribute('userid');
    
    $c = new Criteria;
    $c->add(VersionPeer::OBJECT_ID, $model_id);
    $c->add(VersionPeer::OBJECT, $model);
    $c->addDescendingOrderByColumn(VersionPeer::VERSION);
    $c->setLimit(1);
    $last_version = VersionPeer::doSelectOne($c);
    
    $version = new Version;
    
    $version_no = 1;
    if ($last_version) {
      $version_no = $last_version->getVersion() + 1;
    }
    
    $version->setMutation('delete');
    $version->setTitle($object->getTitle());
    $version->setCreatedBy('User:'.$user_id);
    $version->setObject($model);
    $version->setObjectId($model_id);
    $version->setVersion($version_no);
    $version->setCulture('base');
    $version->save();
    
  }
  
  public function addVersion(BaseObject $object)
  {
    static $saved = false;
    
    if ($saved) return;
    
    $model = get_class($object);
    $model_id = $object->getId();
    
    if (!$model_id) return;
    
    $saved = true;
    
    $peer = $model.'Peer';
    $i18n_peer = $model.'I18NPeer';
    
    $fields = call_user_func_array(array($peer, 'getFieldNames'), array());

    $user_id = sfContext::getInstance()->getUser()->getAttribute('userid');
    
    $c = new Criteria;
    $c->add(VersionPeer::OBJECT_ID, $model_id);
    $c->add(VersionPeer::OBJECT, $model);
    $c->addDescendingOrderByColumn(VersionPeer::VERSION);
    $c->setLimit(1);
    $last_version = VersionPeer::doSelectOne($c);
    
    $version = new Version;
    
    $version_no = 1;
    if ($last_version) {
      $version_no = $last_version->getVersion() + 1;
      $version->setMutation('update');
    }
    else {
      $version->setMutation('insert');
    }
    
    if(self::$revert) {
      $version->setMutation('revert');
    }
    
    $version->setCreatedBy('User:'.$user_id);
    $version->setTitle($object->getTitle());
    $version->setObject($model);
    $version->setObjectId($model_id);
    $version->setVersion($version_no);
    $version->setCulture('base');
    $version->save();
    
    foreach ($fields as $field) {
      $getter = 'get'.$field;
      $value = $object->$getter();
      $version_attribute = new VersionAttribute;
      $version_attribute->setVersionId($version->getId());
      $version_attribute->setName($field);
      $version_attribute->setValue($value);
      $version_attribute->save();
    }
    
    $getter = 'get'.$model.'I18Ns';
    
    if (method_exists($object, $getter)) {
      foreach ($object->$getter() as $object_i18n) {
        
        if (!strlen($object_i18n->getCulture()) == 5) continue;
        
        $version = new Version;
    
        if ($last_version) {
          $version->setMutation('update');
        }
        else {
          $version->setMutation('insert');
        }
        
        $version->setCreatedBy('User:'.$user_id);
        $version->setObject($model);
        $version->setObjectId($model_id);
        $version->setVersion($version_no);
        $version->setCulture($object_i18n->getCulture());
        $version->save();
    
    
      
        $fields = call_user_func_array(array($i18n_peer, 'getFieldNames'), array());
        foreach ($fields as $field) {
          $getter = 'get'.$field;
          $value = $object->$getter();
          $version_attribute = new VersionAttribute;
          $version_attribute->setVersionId($version->getId());
          $version_attribute->setName($field);
          $version_attribute->setValue($value);
          $version_attribute->save();
        }
      }
    }
  }
  
  public function loadVersion($object, $version_no = 1)
  {
    $model = get_class($object);
    $model_id = $object->getId();
    
    if (!$model_id) return;

    $c = new Criteria;
    $c->add(VersionPeer::OBJECT, $model);
    $c->add(VersionPeer::OBJECT_ID, $model_id);
    $c->add(VersionPeer::VERSION, $version_no);
    
    $versions = VersionPeer::doSelect($c);
    
    $object->setCulture('nl_NL');
    
    foreach ($versions as $version) {
      if ($version->getCulture() != 'base') {
        $object->setCulture($version->getCulture());
      }
      else {
        $object->setCulture('nl_NL');
      }
      
      // get attributes for this version localisation
      $c->clear();
      $c->add(VersionAttributePeer::VERSION_ID, $version->getId());
      $attributes = VersionAttributePeer::doSelect($c);
      foreach ($attributes as $attribute) {
        $setter = 'set'.$attribute->getName();
        if (in_array($setter, array('setId', 'setCulture'))) continue;
        $object->$setter($attribute->getValue());
      }
    }
    
    if (self::$revert) {
      $object->save();
    }
  }
  
  public function revertVersion($object, $version_no = 1) {
    
    $model = get_class($object);
    $model_id = $object->getId();
    
    if (!$model_id) return;
    
    $c = new Criteria;
    $c->add(VersionPeer::OBJECT, $model);
    $c->add(VersionPeer::OBJECT_ID, $model_id);
    $c->add(VersionPeer::VERSION, $version_no);
    $c->add(VersionPeer::CULTURE, 'base');
    $version = VersionPeer::doSelectOne($c);
    
    if ($version->getMutation() == 'delete') {
      // load version before this one, because this one is the delete action without any contents
      $c->add(VersionPeer::VERSION, $version_no, Criteria::LESS_THAN );
      $c->addDescendingOrderByColumn(VersionPeer::VERSION);
      
      $version = VersionPeer::doSelectOne($c);
      
      return $this->revertVersion($object, $version->getVersion());
    }
    
    self::$revert = true;
    
    $this->loadVersion($object, $version_no);
    
    $object->save();
  }
}
