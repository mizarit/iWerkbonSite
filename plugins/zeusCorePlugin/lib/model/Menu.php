<?php

class Menu extends BaseMenu
{
}

$columns_map = array(
  'left'   => MenuPeer::TREE_LEFT,
  'right'  => MenuPeer::TREE_RIGHT,
  'parent' => MenuPeer::TREE_PARENT,
  'scope'  => MenuPeer::SCOPE);

sfPropelBehavior::add('Menu', array('zeusnestedset' => array('columns' => $columns_map)));
