<?php

class PagePeer extends BasePagePeer
{
  public static function getPartial($id)
  {
    $page = PagePeer::retrieveByPk($id);
    $page->setCulture(sfContext::getInstance()->getUser()->getCulture());
    return $page->getContent();
  }
}
