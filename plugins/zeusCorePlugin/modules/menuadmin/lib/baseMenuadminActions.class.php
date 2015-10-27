<?php

class baseMenuadminActions extends zeusActions
{
  protected $model = 'Menu';
  
  public function executeTree()
  {
    $root = $this->getRoot();
    
    $node = array(
      'title' => 'Hoofdmenu',
      'uiProvider' => 'col',
      'iconCls' => 'task', 
      'drabbable' => false
    );
    
    if ($root->hasChildren()) {
      $node['children'] = $this->recurseNode($root);
    }
    
    echo json_encode(array($node));
    exit;
  }
  
  public function preSave($object)
  {
    if (!$object->getId() || !$object->getTreeParent()) {
      $root = $this->getRoot();
      $object->insertAsLastChildOf($root);
    }
    
    $object->setValue($this->getRequestParameter('link-'.$this->getRequestParameter('type')));
  }
  
  public function executeSaveorder()
  {
    $data = json_decode($this->getRequestParameter('data'));
    $data2 = json_decode($this->getRequestParameter('f'));
    $root = $this->getRoot();
    var_dump($root);
    var_dump($data);
    var_dump($data2);
    $dataset = $this->recurseSaveorder($data->children[0], $root);
    print_r($dataset);
    exit;
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    // requires some extra steps to link any children to the parent of the deleted record
    $object = MenuPeer::retrieveByPk($this->getRequestParameter('id'));
    if ($object) {
      if ($object->hasChildren()) {
        $parent = $object->getParent();
      
        $children = $object->getChildren();
        foreach ($children as $child) {
          $child->insertAsLastChildOf($parent);
          $child->save();
        }
        
      }
      
      
    }
    
    parent::executeDelete($request);
  }
  
  private function getRoot()
  {
    $c = new Criteria;
    $c->add(MenuPeer::TREE_PARENT, null, Criteria::ISNULL);
    $root = MenuPeer::doSelectOne($c);
    return $root;
  }
  
  private function recurseNode($node)
  {
    $nodes = array();
    
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('ZeusRoute', 'Url'));
    
    foreach ($node->getChildren() as $child) {
      
      if ($child->getType() == 'intern') {
        if (strpos($child->getValue(), ':')) {
          // like Page:1
          list($model, $id) = explode(':', $child->getValue());
          $peer = $model.'Peer';
          $test = call_user_func_array(array($peer, 'retrieveByPk'), array($id));
        }
        else {
          // like @something
          $test = true;
        }
        $url = $test ? route_for($child->getValue()) : '';
      }
      else {
        $url = $child->getValue();
      }
      $url = str_replace('/backend.php', '', $url);
      $url = str_replace('/backend_dev.php', '', $url);
      $url = str_replace('/frontend_dev.php', '', $url);
      
      $value = sfConfig::get('sf_environment') == 'dev' ? $url.' ('.$child->getValue().')' : $url;
      
      $current = array(
        'title' => $child->getTitle(),
        'value' => $value,
        'id' => $child->getId(),
        'uiProvider' => 'col',
        'iconCls' => 'task',
        'expanded' => !$child->hasChildren(),
        'loaded' => !$child->hasChildren()
      );
      
      if ($child->hasChildren()) {
        $current['children'] = $this->recurseNode($child);
      }
      
      $nodes[] = $current;
    }
    
    return $nodes;
  }
  
  private function recurseSaveorder($node, $parent)
  {
    $ret = array();
    
    foreach ($node->children as $child)
    {
      $current = array('id' => $child->id, 'title' => $child->title);
      
      $object = MenuPeer::retrieveByPk($child->id);
      $object->insertAsLastChildOf($parent);
      $object->save();
      
      if (isset($child->children)) {
        $current['children'] = $this->recurseSaveorder($child, $object);
      }
      
      $ret[] = $current;
    }
    
    return $ret;
    
  }
}