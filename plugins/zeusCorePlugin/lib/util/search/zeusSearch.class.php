<?php

class zeusSearch
{
  protected $index = 'default';
  
  public static function getInstance()
  {
    return new zeusSearch();
  }
  
  public function search($query)
  {
    $results = array();
    $results['hits'] = array();
    
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../../vendor');
    require_once 'Zend/Search/Lucene.php';

    //open the index
    $index = new Zend_Search_Lucene(sfConfig::get('sf_data_dir').'/index/'.$this->index);

    
    $hits = $index->find($query);
    
    $results['total_docs'] = $index->count();
    
    foreach ($hits as $hit) {
      $results['hits'][$hit->object_id] = array(
        'id'      => $hit->object_id,
        'object'  => $hit->object,
        'title'   => $hit->title,
        'score'   => $hit->score
      );
    }
    
    $results['total_hits'] = count($results['hits']);
    
    return $results;
  }
}