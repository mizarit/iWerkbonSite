<?php

class sfSearchIndexTask extends sfBaseTask
{
  protected
    $config = null;
    
  protected $index = 'default';

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The environment', null),
      new sfCommandOption('type', null, sfCommandOption::PARAMETER_OPTIONAL, 'The type', 'all'),
    ));

    $this->aliases = array('si', 'search-index');
    $this->namespace = 'search';
    $this->name = 'index';
    $this->briefDescription = 'Populates the search index';

    $this->detailedDescription = <<<EOF
The [search:index|INFO] populates the search index.

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../../vendor');
    set_include_path(get_include_path().PATH_SEPARATOR.sfConfig::get('sf_root_dir').'/lib/vendor/symfony/lib/plugins/sfPropelPlugin/lib/vendor');
    require_once 'Zend/Search/Lucene.php';
    require_once 'propel/Propel.php';
    
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    // include config to add behaviors
    $configs = glob(sfConfig::get('sf_plugins_dir'). '/*/config/config.php');
    foreach ($configs as $config) {
      include($config);
    }
    
    $this->logSection('search', sprintf('Indexing index %s', $this->index));
    
    $index = new Zend_Search_Lucene(sfConfig::get('sf_data_dir').'/index/'.$this->index, true);

    $indexers = glob(sfConfig::get('sf_plugins_dir'). '/*/lib/util/search/zeusSearchIndexer*');
    foreach ($indexers as $indexer_path) {
      $indexer = substr(basename($indexer_path),0,-10);
      if ($indexer != 'zeusSearchIndexerBase') {
        require_once($indexer_path);
        $model = substr($indexer, 17);
        $peer = $model.'Peer';
        $model_i18n = $model.'I18N';
        $peer_i18n = $model.'I18NPeer';
    
        $objects = array();
        
        $i18n = count(glob(sfConfig::get('sf_plugins_dir'). "/*/lib/model/{$model_i18n}.php")) > 0;
        if (!$i18n) {
          $i18n = count(glob(sfConfig::get('sf_root_dir'). "/lib/model/{$model_i18n}.php")) > 0;
        }
        
        if ($i18n) {
          $objects = call_user_func_array(array($peer, 'doSelectWithI18N'), array(new Criteria, 'nl_NL'));
        }
        else if (class_exists($peer)){
          $objects = call_user_func_array(array($peer, 'doSelect'), array(new Criteria));
        }
        
        $object_indexer = new $indexer();
        foreach ($objects as $object) {
          if ($i18n) {
            $object->setCulture('nl_NL');
          }
          $this->logSection('search', sprintf('Adding document %s:%s, %s', get_class($object), $object->getId(), $object->getTitle()));
          $index->addDocument($object_indexer->getLuceneDocument($object));
        }
      }
    }
    
    $index->commit();

    $this->logSection('search', sprintf('Indexed %s documents', $index->count()));
    
    
  }
}
