<?php


/**
 * Skeleton subclass for representing a row from the 'appointment' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Tue Oct  6 11:50:05 2015
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class Appointment extends BaseAppointment {

  public function getColor()
  {
    $color = parent::getColor() + 1;
    if ($color > 6) {
      $color = $color % 6;
      $this->setColor($color);
      $this->save();
    }
    return $color - 1;

  }
} // Appointment
