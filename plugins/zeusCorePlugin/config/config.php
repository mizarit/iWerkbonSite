<?php

/**
 * 
 * Zeus Versions
 * 
 */


sfPropelBehavior::registerHooks('zeusversions', array(
  ':save:pre'      => array('zeusVersionBehavior', 'preSave'),
  ':save:post'     => array('zeusVersionBehavior', 'postSave'),
  ':delete:post'   => array('zeusVersionBehavior', 'postDelete'),
  ':delete:pre'   => array('zeusVersionBehavior', 'preDelete')
));

sfPropelBehavior::registerMethods('zeusversions', array (
  array (
    'zeusVersionBehavior',
    'addVersion'
  ),
  array (
    'zeusVersionBehavior',
    'loadVersion'
  ),
  array (
    'zeusVersionBehavior',
    'revertVersion'
  )
));


/**
 * 
 * Zeus Search
 * 
 */

sfPropelBehavior::registerHooks('zeussearch', array(
  ':save:post'   => array('zeusSearchBehavior', 'postSave'),
  ':delete:pre' => array('zeusSearchBehavior', 'preDelete')
));


/**
 * 
 * Zeus Nested set
 * 
 */

sfPropelBehavior::registerHooks('zeusnestedset', array(
  ':save:pre'   => array('zeusNestedsetBehavior', 'preSave'),
  ':delete:pre' => array('zeusNestedsetBehavior', 'preDelete'),  
));

sfPropelBehavior::registerMethods('zeusnestedset', array (
  array (
    'zeusNestedsetBehavior',
    'getLeftValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'getRightValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'getParentIdValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'getScopeIdValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'setLeftValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'setRightValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'setParentIdValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'setScopeIdValue'
  ),
  array (
    'zeusNestedsetBehavior',
    'makeRoot'
  ),
  array (
    'zeusNestedsetBehavior',
    'insertAsFirstChildOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'insertAsLastChildOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'insertAsNextSiblingOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'insertAsPrevSiblingOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'insertAsParentOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'hasChildren'
  ),
  array (
    'zeusNestedsetBehavior',
    'getChildren'
  ),
  array (
    'zeusNestedsetBehavior',
    'getParent'
  ),
  array (
    'zeusNestedsetBehavior',
    'getNumberOfChildren'
  ),
  array (
    'zeusNestedsetBehavior',
    'getDescendants'
  ),
  array (
    'zeusNestedsetBehavior',
    'getNumberOfDescendants'
  ),
  array (
    'zeusNestedsetBehavior',
    'isRoot'
  ),
  array (
    'zeusNestedsetBehavior',
    'hasParent'
  ),
  array (
    'zeusNestedsetBehavior',
    'hasNextSibling'
  ),
  array (
    'zeusNestedsetBehavior',
    'hasPrevSibling'
  ),
  array (
    'zeusNestedsetBehavior',
    'isLeaf'
  ),
  array (
    'zeusNestedsetBehavior',
    'isEqualTo'
  ),
  array (
    'zeusNestedsetBehavior',
    'isChildOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'isDescendantOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'moveToFirstChildOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'moveToLastChildOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'moveToNextSiblingOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'moveToPrevSiblingOf'
  ),
  array (
    'zeusNestedsetBehavior',
    'deleteChildren'
  ),
  array (
    'zeusNestedsetBehavior',
    'deleteDescendants'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrieveFirstChild'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrieveLastChild'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrieveNextSibling'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrievePrevSibling'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrieveParent'
  ),
  array (
    'zeusNestedsetBehavior',
    'retrieveSiblings'
  ),
  array (
    'zeusNestedsetBehavior',
    'getLevel'
  ),
  array (
    'zeusNestedsetBehavior',
    'setLevel'
  ),
  array (
    'zeusNestedsetBehavior',
    'getPath'
  ),
  array (
    'zeusNestedsetBehavior',
    'reload'
  ),
));
