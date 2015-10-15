<?php

class myUser extends sfBasicSecurityUser
{
  public function getUsername()
  {
    return 'admin';
  }
}
