<?php

class Version extends BaseVersion
{
  public function getMutationStr()
  {
    switch($this->getMutation()) {
      case 'insert':
        return 'Aangemaakt';
      case 'update':
        return 'Gewijzigd';
      case 'delete':
        return 'Verwijderd';
      case 'revert':
        return 'Hersteld';
    }
  }
  
  public function getCreatedByStr()
  {
    list($user, $user_id) = explode(':', $this->getCreatedBy());
    $user = UserPeer::retrieveByPk($user_id);
    if ($user) {
      return $user->getTitle();
    }
    
    return 'Deleted user';
  }
}
