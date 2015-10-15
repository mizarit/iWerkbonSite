<?php

/**
 * This is an auto-generated SoapHandler. All changes to this file will be overwritten.
 */
class BaseapisoapHandler extends ckSoapHandler
{
  public function apisoap_update($company)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'update', array($company));
  }

  public function apisoap_getLastminutes()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getLastminutes', array());
  }

  public function apisoap_getCategories()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getCategories', array());
  }

  public function apisoap_getApptypes()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getApptypes', array());
  }

  public function apisoap_getApptypePricing()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getApptypePricing', array());
  }

  public function apisoap_getApptypeTimes()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getApptypeTimes', array());
  }

  public function apisoap_getConsumer()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getConsumer', array());
  }

  public function apisoap_setConsumer()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'setConsumer', array());
  }

  public function apisoap_prepareAppointment()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'prepareAppointment', array());
  }

  public function apisoap_preparePayment()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'preparePayment', array());
  }

  public function apisoap_finalizeAppointment()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'finalizeAppointment', array());
  }

  public function apisoap_getIssuers()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('apisoap', 'getIssuers', array());
  }
}