<?php

class companyadminActions extends zeusActions
{
  protected $model = 'Company';
  
  public function postSave($object)
  {
    $c = new Criteria;
    $c->add(ConnectionPeer::COMPANY_ID, $object->getId());
    $c->add(ConnectionPeer::DATATYPE, 'appointments');
    $connection = ConnectionPeer::doSelectOne($c);
    if (!$connection) {
      $connection = new Connection;
      $connection->setDatatype('appointments');
      $connection->setCompanyId($object->getId());
    }
    $connection->setApiServer($this->getRequestParameter('api_server'));
    $connection->setApiKey($this->getRequestParameter('api_key'));
    $connection->setApiSecret($this->getRequestParameter('api_secret'));
    $connection->setAdapter($this->getRequestParameter('adapter'));
    $connection->save();
  }
}