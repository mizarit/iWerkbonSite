<?php 

class baseNewsadminActions extends zeusActions
{
  protected $model = 'News';
  
  public function preSave($object)
  {
    $sites = array();
    foreach ($_POST as $key => $value) {
      if (substr($key, 0, 5) == 'site_') {
        $sites[] = substr($key, 5);
      }
    }
    
    $object->setSites(implode('|', $sites));
  }
  
  public function executeImport()
  {
    $rss = new zeusRSSParser;
    
    $feed = FeedPeer::retrieveByPk($this->getRequestParameter('id'));
    if (!$feed) {
      $this->redirect('newsadmin/importfeeds');
    }
    
    $items = array();
    
	  $rss = fetch_rss($feed->getUrl());

  	foreach ($rss->items as $key => $item) {
  	  $items[] = array(
  	    $key,
  	    '<input type="checkbox" class="checkbox" name="feed-'.$key.'" id="feed-'.$key.'">',
  	    $item['title'],
  	    isset($item['description']) ? strip_tags($item['description']) : '',
  	    $item['link']
  	  );
  	}
  	
  	if ($this->getRequest()->getMethod() == 'POST')
  	{
  	  $selected = array();
  	  foreach ($_POST as $key => $value)
  	  {
  	    if (substr($key,0,5) == 'feed-') {
  	      $selected[] = substr($key,5);
  	    }
  	  }
  	  
  	  $c = new Criteria;
  	  foreach ($selected as $item_id)
  	  {
  	    $item = $items[$item_id];
  	    list($k, $chk, $title, $description, $link) = $item;
  	    // check if it already exists
  	    $description = trim($description);
  	    
  	    $c->clear();
  	    $c->add(NewsI18NPeer::TITLE, $title);
  	    $c->add(NewsI18NPeer::CONTENTSHORT, $description);
  	    $test = NewsI18NPeer::doSelectOne($c);
  	    
  	    if (!$test) {
  	      // new item
  	      
  	      $object = new News;
  	      $object->setCulture('nl_NL');
  	      $object->setTitle($title);
  	      $object->setContentshort($description);
  	      $html = '<p>'.str_replace("\n", '</p><p>', $description).'</p><p><a href="'.$link.'">Lees het hele bericht</a>';
  	      $html = str_replace('<p></p>', '', $html);
  	      $object->setContent($html);
  	      $object->setDate(time());
  	      $object->setVisible(true);
  	      $object->setSites('');
  	      $object->save();
  	    }
  	  }
  	  
  	  $this->redirect('newsadmin/index');
  	}
    
    $response = sfContext::getInstance()->getResponse();
  	$response->addStylesheet('/zeusCore/js/extjs/resources/css/ext-all.css');
  	$response->addStylesheet('/zeusCore/js/extjs/resources/css/xtheme-slate.css');
  	$response->addJavascript('/zeusCore/js/extjs/adapter/ext/ext-base.js');
  	$response->addJavascript('/zeusCore/js/extjs/ext-all.js');
  	$response->addJavascript('/zeusCore/js/extjs/src/locale/ext-lang-nl.js');
  	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SlidingPager.js');
  	$response->addJavascript('/zeusCore/js/extjs/examples/ux/SliderTip.js');
  	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PanelResizer.js');
  	$response->addJavascript('/zeusCore/js/extjs/examples/ux/PagingMemoryProxy.js');
    
  	$response->addJavascript('/zeusCore/js/prototype/prototype.js', 'first');
    $this->data = $items;
  }
  
  public function executeImportfeeds()
  {
    $c = new Criteria;
    $feeds = FeedPeer::doSelect($c);
    $items = array();
    foreach ($feeds as $feed) {
      $items[] = array($feed->getId(), $feed->getUrl());
    }
    $this->data = $items;
  }
  
  public function executeCreatefeed()
  {
    if ($this->getRequest()->getMethod() == 'POST')
    {
      if ($this->getRequestParameter('url') != '') {
        $feed = new Feed;
        $feed->setUrl($this->getRequestParameter('url'));
        $feed->save();
      }
      
      $this->redirect('newsadmin/importfeeds');
    }
  }
  
  public function executeDeletefeed()
  {
    $feed = FeedPeer::retrieveByPk($this->getRequestParameter('id'));
    if ($feed) {
      $feed->delete();
    }
    
    $this->redirect('newsadmin/importfeeds');
  }
}