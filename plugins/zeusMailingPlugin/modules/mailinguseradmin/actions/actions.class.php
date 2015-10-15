<?php

class mailinguseradminActions extends zeusActions
{
  protected $model = 'Mailinguser';
  
  public function postSave($object)
  {
    $c = new Criteria;
    $c->add(SubscriptionPeer::MAILINGUSER_ID, $object->getId());
    SubscriptionPeer::doDelete($c);
    foreach ($_POST as $key => $value) {
      if (substr($key, 0, 15) == 'mailinglist_id_') {
        $mailing_list_id = substr($key, 15);
        $subscription = new Subscription;
        $subscription->setMailinguserId($object->getId());
        $subscription->setMailinglistId($mailing_list_id);
        $subscription->save();
      }
    }
  }
  
  public function executeImport()
  {
    // mailinglist 9 = klanten systeem 2.0
    // mailinglist 15 = groupon be
    // mailinglist 13 = groupon nl
    $files[2] = sfConfig::get('sf_root_dir').'/web/docs/adressen.csv';
    $files[6] = sfConfig::get('sf_root_dir').'/web/docs/importbsd.csv';
    $files[15] = sfConfig::get('sf_root_dir').'/web/docs/import-20120125-groupon-be.csv';
    $files[13] = sfConfig::get('sf_root_dir').'/web/docs/import-20120125-groupon-nl.csv';
    $file = file_get_contents($files[$category]);
    
    $category = $_GET['group'];
    $lines = explode("\r", $file);

    $headers = array();
    $subscriptions = array();
    
    foreach ($lines as $line) {
      $row = explode(';', $line);
      if (count($headers) == 0) {
        $headers = $row;
      }
      else {
        $subscriptions[] = array(
          'title' => trim($row[array_search('name', $headers)]).' '.trim($row[array_search('title', $headers)]),
          'name' => trim($row[array_search('name', $headers)]),
          'email' => $row[array_search('email', $headers)]
        );
      }
    }
    
    $imported = 0;
    
    $c = new Criteria;
    
    foreach ($subscriptions as $subscription)
    {
      $c->clear();
      $c->add(MailinguserPeer::EMAIL, $subscription['email']);
      $mailinguser = MailinguserPeer::doSelectOne($c);
      if (!$mailinguser) {
        // user does not exist, import it
        $mailinguser = new Mailinguser;
        $mailinguser->setTitle($subscription['title']);
        $mailinguser->setName($subscription['name']);
        $mailinguser->setEmail($subscription['email']);
        $mailinguser->save();
        
        $subscription_o = new Subscription;
        $subscription_o->setMailinguserId($mailinguser->getId());
        
        $subscription_o->setMailinglistId($category);
        $subscription_o->save();
        
        $imported++;
      }
    }
    
    echo 'Klaar met het importeren van '.$imported.' gebruikers.';
    exit;
  }
}