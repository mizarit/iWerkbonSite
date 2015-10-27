<?php

class zeusSearchIndexerBase
{
  public function getLuceneDocument($object)
  {
    $doc = new Zend_Search_Lucene_Document();
    foreach ($this->fields as $field => $cfg) {
      $getter = 'get'.ucfirst($field);

      if (method_exists($object, $getter)) {
        switch ($cfg['type']) {
          case 'keyword':
            $doc->addField(Zend_Search_Lucene_Field::Keyword($field, htmlentities(strip_tags(($object->$getter())))));
            break;
            
          case 'text':
            $doc->addField(Zend_Search_Lucene_Field::Text($field, htmlentities(strip_tags(($object->$getter())))));
            break;
            
          case 'unstored':
            $doc->addField(Zend_Search_Lucene_Field::Unstored($field, htmlentities(strip_tags(($object->$getter())))));
            break;
            
          case 'unindexed':
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed($field, htmlentities(strip_tags(($object->$getter())))));
            break;
            
          case 'binary':
            $doc->addField(Zend_Search_Lucene_Field::Binary($field, htmlentities(strip_tags(($object->$getter())))));
            break;
        }
      }
    }
    
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('object_id', $object->getId()));
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('object', get_class($object)));
    return $doc;
  }
}