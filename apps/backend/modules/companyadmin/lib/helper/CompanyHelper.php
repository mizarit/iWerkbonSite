<?php



function company_edit_api($object, $config = array())
{
  
  $c = new Criteria;
  $c->add(ConnectionPeer::COMPANY_ID, $object->getId());
  $c->add(ConnectionPeer::DATATYPE, 'appointments');
  $connection = ConnectionPeer::doSelectOne($c);
  if (!$connection) {
    $connection = new Connection;
  }

  echo $object->getId();
  echo $object->getCalendarId();
  $options = array();
  $ret = form_row('api_server', input_tag('api_server', $connection->getApiServer(), $options), array('label' => 'API server'));
  $ret .= form_row('api_key', input_tag('api_key', $connection->getApiKey(), $options), array('label' => 'API key'));
  $ret .= form_row('api_secret', input_tag('api_secret', $connection->getApiSecret(), $options), array('label' => 'API secret'));
  $ret .= form_row('adapter', select_tag('adapter', options_for_select(array('onlineafspraken' => 'OnlineAfspraken'), $connection->getAdapter()), $options), array('label' => 'Adapter'));
  
  return $ret;
}

/*

 company_id:
    adapter:          varchar(16)
    api_key:          varchar(255)
    api_secret:       varchar(255)
    api_server:       varchar(255)
    datatype:         varchar(16)
    active:           boole
    */