<?php

class partialadminActions extends basePageadminActions
{
  public function executeCreate(sfWebRequest $request)
  {
    $title = $this->getRequestParameter('title');
    $page = new Page;
    $page->setCulture('nl_NL');
    $page->setTitle($title);
    $page->setHtmltitle('PARTIAL');
    $page->save();
    $this->redirect('partialadmin/edit?id='.$page->getId());
  }
}
