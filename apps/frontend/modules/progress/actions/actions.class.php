<?php

class progressActions extends sfActions
{
  public function executeIndex()
  {
    $challenge = $this->getRequestParameter('id');
    $c = new Criteria;
    $c->add(ChallengePeer::SHORTNAME, $challenge);
    $challenge = ChallengePeer::doSelectOne($c);
    $this->forward404Unless($challenge);
    
    $this->challenge = $challenge;
    
  }
}