<?php

class errorActions extends sfActions
{
  public function executeError404()
  {
    $this->setLayout(false);
  }
}