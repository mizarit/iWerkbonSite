<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2010, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id: prove.php 26432 2010-01-09 14:11:42Z chrisk $
 */

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once($_SERVER['SYMFONY'].'/vendor/lime/lime.php');
require_once($_SERVER['SYMFONY'].'/util/sfFinder.class.php');

$h = new lime_harness(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__).'/..');

$h->register(sfFinder::type('file')->prune('fixtures')->name('*Test.php')->in(array(
  // unit tests
  $h->base_dir.'/unit',
  // functional tests
  $h->base_dir.'/functional'
)));

exit($h->run() ? 0 : 1);
