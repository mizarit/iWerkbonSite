<?php

class baseRssActions extends sfActions
{
  public function executeFeed()
  {
    $title = zeusConfig::get('RSS Feed', 'Feed titel', 'input', 'MizarIT');
    $webmaster = zeusConfig::get('RSS Feed', 'Webmaster', 'input', 'info@'.$_SERVER['HTTP_HOST']);
    $description = zeusConfig::get('RSS Feed', 'Omschrijving', 'input', 'Nieuwsfeed van '.$_SERVER['HTTP_HOST']);
    $banner = zeusConfig::get('RSS Feed', 'Banner ( volledig pad )', 'input', '');
    //$banner = 'http://www.star-people.nl/bin/news/images/Banners/starpeoplebanner7.jpg';
    
    $link = 'http://'. $_SERVER['HTTP_HOST'];
    
    $rss = new UniversalFeedCreator();
    
    $proxy = ucfirst($this->getRequestParameter('proxy')).'Proxy';
    
    $params = $this->getRequest()->getParameterHolder()->getAll();
    unset($params['module']);
    unset($params['action']);
    unset($params['proxy']);
  
    $proxy = call_user_func_array(array($proxy, 'getInstance'), array($params));
    $objects = $proxy->getItems();
    
    $rss->title = $title.$proxy->getFeedTitle();
    $rss->language = 'nl_NL';
    $rss->webmaster = $webmaster;
    
    $rss->pubDate = date('r');
    
    $image = new FeedImage;
    $image->link = $link;
    $image->url = $banner;
    $rss->image = $image;
    
    $rss->link = $link;
    $rss->description = $description;
    $rss->managingEditor = 'MizarIT';
    
    foreach ($objects as $object) {
      $item = new FeedItem();
      $item->title = $object['title'];
      $item->date = date('r', $object['pubdate']);
      $item->link = $link.$object['link'];
      $item->description = $object['description'];
      $item->category = $object['category'];
      $rss->addItem($item);
    }

    $this->rss = $rss;
  }
}