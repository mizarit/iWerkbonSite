<?php

function mailinguser_edit_subscriptions($object, $config = array()) 
{
 
  $c = new Criteria;
  $c->addAscendingOrderByColumn(MailinglistPeer::TITLE);
  $mailinglists = MailinglistPeer::doSelect($c);
  
  $options = array();
  foreach ($mailinglists as $mailinglist)
  {
    $options[$mailinglist->getId()] = $mailinglist->getTitle();
  }
  
  $c->clear();
  $c->add(SubscriptionPeer::MAILINGUSER_ID, $object->getId());
  $subscriptions = SubscriptionPeer::doSelect($c);
  
  $selected = array();
  foreach ($subscriptions as $subscription)
  {
    $selected[] = $subscription->getMailinglistId();
  }
  
  $ret = '';
  
  foreach ($options as $option_id => $option)
  {
    $checked = in_array($option_id, $selected) ? ' checked="checked"' : '';
    $id = 'mailinglist_id_'.$option_id;
    
    $ret .= '<input '.$checked.' class="checkbox" type="checkbox" id="'.$id.'" name="'.$id.'"> <label for="'.$id.'">'.$option.'</label> ';
  }
  
   
  
  
  return form_row('subscriptions', $ret, $config);
  
}

function mailinguser_list_mailinglists($object, $config = array()) 
{
  $c = new Criteria;
  static $mailinglists = false;
  
  if (!$mailinglists) {
    $mailinglists_o = MailinglistPeer::doSelect($c);
    foreach ($mailinglists_o as $mailinglist_o)
    {
      $mailinglists[$mailinglist_o->getId()] = $mailinglist_o->getTitle();
    }
  }
  
  $c->clear();
  $c->add(SubscriptionPeer::MAILINGUSER_ID, $object->getId());
  $c->addAscendingOrderByColumn(SubscriptionPeer::MAILINGLIST_ID);
  $lists = array();
  $subscriptions = SubscriptionPeer::doSelect($c);
  foreach ($subscriptions as $subscription)
  {
    $lists[] = $mailinglists[$subscription->getMailinglistId()];
  }
  
  return implode(', ', $lists);
}