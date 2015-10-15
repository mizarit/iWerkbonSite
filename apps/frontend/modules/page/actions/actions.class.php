<?php

class pageActions extends basePageActions
{
  public function executeIndex(sfWebRequest $request)
  {
    parent::executeIndex($request);
    pvCrumblepath::add('@lastminutes', 'last-minutes');
    pvCrumblepath::add($this->page, strtolower($this->page->getTitle()));
  }
}