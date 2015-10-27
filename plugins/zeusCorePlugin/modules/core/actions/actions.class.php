<?php

class coreActions extends zeusActions  
{
  protected $model = 'Demo';
  
  public function executeAnalyzer()
  {
    $content = trim(strip_tags($this->getRequestParameter('content')));
    $v = zeusAnalyzer::getKeywords($content);
  
    echo $v;
    exit;
  }
  
  public function executeScraps()
  {
    $user = UserPeer::retrieveByPk($this->getUser()->getAttribute('userid'));
    $user->setScraps($this->getRequestParameter('scraps'));
    $user->save();
    exit;
  }
  
  public function executeAddFavorite()
  {
    $c = new Criteria;
    $c->add(FavoritePeer::TITLE, $this->getRequestParameter('title'));
    $c->add(FavoritePeer::ACTIONURL, $this->getRequestParameter('actionurl'));
    if (FavoritePeer::doSelectOne($c)) {
      echo 'Deze actie was al toegevoegd aan je favoriete acties.';
    }
    else {
      $fav = new Favorite;
      $fav->setTitle($this->getRequestParameter('title'));
      $fav->setActionurl($this->getRequestParameter('actionurl'));
      $fav->save();
      echo 'De actie is toegevoegd aan je favoriete acties.';
    }
    exit;
  }
  
  public function executePing()
  {
    echo 'pong';
    exit;
  }
}