<?php

class zeusSearchBehavior
{
  public function postSave(BaseObject $object)
  {
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../vendor');
    
    $is_indexable = true;
    
    if (method_exists($object, 'isIndexable')) {
      $is_indexable = $object->isIndexable();
    }
    
    if ($is_indexable) {
      $index = new Zend_Search_Lucene(sfConfig::get('sf_data_dir').'/index/default');
      $indexer = 'zeusSearchIndexer'.get_class($object);
      
      $object_indexer = new $indexer();
      $index->addDocument($object_indexer->getLuceneDocument($object));
      $index->commit();
    }
  }
  
  public function preDelete(BaseObject $object)
  {
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../vendor');
    
    $index = new Zend_Search_Lucene(sfConfig::get('sf_data_dir').'/index/default');
    
    $object_id  = new Zend_Search_Lucene_Index_Term($object->getId(), 'object_id');
    $object_class  = get_class($object);
    $query = new Zend_Search_Lucene_Search_Query_Term($object_id);  
 
    $hits  = $index->find($query);  
   
    foreach ($hits AS $hit)   
    {    
      if ($hit->object == $object_class) {
        $index->delete($hit->id);    
      }
    }  
  }
}