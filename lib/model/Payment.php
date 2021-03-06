<?php


/**
 * Skeleton subclass for representing a row from the 'payment' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Thu Oct  1 13:44:07 2015
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class Payment extends BasePayment {

  public function getPaymethodStr()
  {
    $map = array(
      'pin' => 'Pin-betaling',
      'cash' => 'Contant',
      'invoice' => 'Op rekening'
    );
    return $map[$this->getPaymethod()];
  }
} // Payment
