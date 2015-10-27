<?php

class Page extends BasePage
{
  public function inSitemap()
  {
    return ($this->getHtmltitle() != 'PARTIAL');
  }
}

zeusPropelBehavior::add('Page', array('zeussearch', 'zeusversions'));