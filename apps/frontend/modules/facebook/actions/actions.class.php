<?php
class facebookActions extends lastminuteActions
{
  public function preExecute()
  {
    //echo urlencode(json_encode(array('content' => 'actie-tenchi')));
    //exit;

    if (isset($_REQUEST['app_data'])) {
      $this->getUser()->setAttribute('app_data', $_REQUEST['app_data']);
    }
    
    $this->setLayout('layout-facebook');
    if ($this->hasRequestParameter('deal')) {
      $url = '/lastminutes/'.$this->getRequestParameter('company_name').'/'.$this->getRequestParameter('deal');
      $c = new Criteria;
      $c->add(RoutePeer::URL, $url);
      $route = RoutePeer::doSelectOne($c);
      $this->forward404Unless($route);
      $peer = $route->getObject().'Peer';
      $object = call_user_func_array(array($peer, 'retrieveByPk'), array($route->getObjectId()));
      $object->setCulture('nl_NL');
      $this->object = $object;
    }
    $app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
    $app_id = $app['all']['facebook']['appid'];
    $user = false;
    
    //$canvas_page = 'http://'.$_SERVER['SERVER_NAME'].'/facebook?fb=1';
    
    if (strpos($_SERVER['SERVER_NAME'], 'mizar')) {
      $canvas_page = 'http://apps.facebook.com/plekjevrijdev';
    } 
    else {
      $canvas_page = 'http://apps.facebook.com/plekjevrij-consument';
    }
    
    $auth_url = "http://www.facebook.com/dialog/oauth?client_id={$app_id}&scope=email,publish_actions&redirect_uri=".urlencode($canvas_page);

    if ($this->hasRequestParameter('fb')) {
    
      $signed_request = $_REQUEST["signed_request"];
  
      list($encoded_sig, $payload) = explode('.', $signed_request, 2);
  
      $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
  
      if ($this->hasRequestParameter('code') && !$this->hasRequestParameter('fb')) {
        
        if (strpos($_SERVER['SERVER_NAME'], 'mizar')) {
          $this->redirect('http://apps.facebook.com/plekjevrij-consument');
        }
        else {
          $this->redirect('http://apps.facebook.com/plekjevrijdev');
        }
      }
      elseif (empty($data['user_id'])) {
        echo '<script>top.location.href=\''.$auth_url.'\'</script>';
      } 
      else {
        $graph_url = 'https://graph.facebook.com/me?access_token='.$data['oauth_token'].'&expires='.($data['expires']-$data['issued_at']);
        $user = json_decode(file_get_contents($graph_url));
  
        if ($user->hometown) {
          list($city, $country) = explode(',', $user->hometown->name);
          $user->city = $city;
          $user->country = $country;
        }
  
        $this->user = $user;
        
        if ($user) {
          $this->getUser()->setAttribute('name', $user->name);
          $this->getUser()->setAttribute('email', $user->email);
          $this->getUser()->setAttribute('signed_request', $_REQUEST['signed_request']);
        }
      }
    }
    else {
      $signed_request = $this->getUser()->getAttribute('signed_request');
      if ($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);
    
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
          
        $graph_url = 'https://graph.facebook.com/me?access_token='.$data['oauth_token'].'&expires='.($data['expires']-$data['issued_at']);
        $graph = file_get_contents($graph_url);
        if (!$graph) {
          // expired
          $app_id = $app['all']['facebook']['appid'];
          $user = false;
        
          $canvas_page = 'http://'.$_SERVER['SERVER_NAME'].'/facebook?fb=1';
        
          $auth_url = "http://www.facebook.com/dialog/oauth?client_id={$app_id}&scope=email,publish_actions&redirect_uri=".urlencode($canvas_page);
          //echo '<script>top.location.href=\''.$auth_url.'\'</script>';
          //exit;
        }
        else {
          $user = json_decode($graph);
          
          if ($user->hometown) {
            list($city, $country) = explode(',', $user->hometown->name);
            $user->city = $city;
            $user->country = $country;
          
          }
          $this->user = $user;
        }
      }
    }
    
    if (isset($this->user->email)) {
      if ($this->getUser()->hasAttribute('app_data')) {
        $_REQUEST['app_data'] = $this->getUser()->getAttribute('app_data');
        $this->getUser()->setAttribute('app_data', null);
      }
    
      if (isset($_REQUEST['app_data'])) {
        $params = json_decode($_REQUEST['app_data']);
        if (isset($params->content)) {
          $this->redirect('facebook/content?p='.$params->content);
          exit;
        }
      }
    }
    
    if (!isset($this->user->email)) {
      // force permissions
      echo '<script>top.location.href=\''.$auth_url.'\'</script>';
      exit;
    }
    //https://apps.facebook.com/plekjevrijdev/?app_data={%22content%22%3A%22actie-tenchi%22}
      
    if (isset($this->user) && isset($this->user->email)) {
      $consumer = new Consumer;
      $consumer->setEmail($this->user->email);
      
      $contact = ContactPeer::retrieveFor($consumer);
      $contact->setHasmailing(true);
      $contact->save();
    
      $c = new Criteria;
      $c->add(SubscriptionPeer::MAILINGLIST_ID, 9);
      $c->add(SubscriptionPeer::CONTACT_ID, $contact->getId());
      $subscription = SubscriptionPeer::doSelectOne($c);
      if (!$subscription) {
        $subscription = new Subscription;
        $subscription->setMailinglistId(9);
        $subscription->setContactId($contact->getId());
        $subscription->save();
      }
    }
  }
  
  public function executeContent()
  {
    $c = new Criteria;
    $c->add(RoutePeer::URL, '/'.$this->getRequestParameter('p'));
    $route = RoutePeer::doSelectOne($c);
    $this->forward404Unless($route);
    $object = call_user_func_array(array($route->getObject().'Peer', 'retrieveByPk'), array($route->getObjectId()));
    $this->forward404Unless($object);
    $this->object = $object;
    
    $errors = array();
    $name = $email = $zipcode = '';
    
    if ($this->getRequest()->getMethod() == 'POST') {
      if ($this->getRequestParameter('email') == '') {
        $errors['email'] = 'E-mailadres is verplicht';
      }
      if ($this->validateField('email', $this->getRequestParameter('email'), array('label' => 'E-mail adres')) !== true) {
        $errors['email'] = 'E-mailadres is niet juist ingevuld';
      }
      if ($this->getRequestParameter('zipcode') == '') {
        $errors['zipcode'] = 'Postcode is verplicht';
      }
      if ($this->validateField('zipcode', $this->getRequestParameter('zipcode'), array('label' => 'Postcode')) !== true) {
        $errors['zipcode'] = 'Postcode is niet juist ingevuld';
      }
      
      if (!$this->hasRequestParameter('agree')) {
        $errors['agree'] = 'Je moet de algemene voorwaarden accepteren';
      }
      
      $name = $this->getRequestParameter('name');
      $email = $this->getRequestParameter('email');
      
      if (count($errors) == 0) {
      
        $c->clear();
        $c->add(ConsumerPeer::EMAIL, $this->getRequestParameter('email'));
        $consumer = ConsumerPeer::doSelectOne($c);
        if (!$consumer) {
          $consumer = new Consumer;
          $consumer->setTitle($this->getRequestParameter('name'));
          $consumer->setLastname($this->getRequestParameter('name'));
          $consumer->setEmail($this->getRequestParameter('email'));
          $consumer->setZipcode($this->getRequestParameter('zipcode'));
          $consumer->save();
        }
        $contact = ContactPeer::retrieveFor($consumer);
        $contact->setHasmailing(true);
        $contact->setOptinmailing(true);
        $contact->save();
      
        $c->clear();
        $c->add(SubscriptionPeer::CONTACT_ID, $contact->getId());
        $c->add(SubscriptionPeer::MAILINGLIST_ID, 8);
        $subscription = SubscriptionPeer::doSelectOne($c);
        if (!$subscription) {
          $subscription = new Subscription;
          $subscription->setContactId($contact->getId());
          $subscription->setMailinglistId(8);
          $subscription->save();
          
          ContactMutationPeer::log($contact, 'mailing', 8, 'subscribe');
        }
        
        $msg = <<<EOT
Er is een nieuwe inschrijving:
        
{$name}
{$email}
{$zipcode}
            
EOT;
        //mail('ricardo.matters@gmail.com', 'Nieuwe inschrijving op FB actie', $msg);
        mail('jorgen.kromhout@plekjevrij.nl', 'Nieuwe inschrijving op FB actie', $msg);
        
        $this->message = 'Bedankt voor je deelname!';
      }
      
    }
    else {
      if($this->user) {
        $name = $this->user->name;
        $email = $this->user->email;
      }
      else if ($this->getUser()->hasAttribute('name')) {
        $name = $this->getUser()->getAttribute('name');
        $email = $this->getUser()->getAttribute('email');
      }
    }
    
    $this->name = $name;
    $this->email = $email;
    $this->zipcode = $zipcode;
    $this->errors = $errors;
    
  }
  
  public function executeIndex()
  {
    // reset ideal return url
    $this->getUser()->setAttribute('return_url', null);
    
    $c = new Criteria;
    $c->add(LastminutePeer::MODUS, 'approved:%', Criteria::LIKE);
      
    $validCompanyIds = array();
    
    if ($this->hasRequestParameter('q') && $this->getRequestParameter('q') != '') {
      $req = $this->getRequestParameter('q');
      $c = new Criteria;
      $c->add(GeocachePeer::Q, $req);
      $geo = GeocachePeer::doSelectOne($c);
      if (!$geo) {
        $lat = 52.148769;
        $lon = 4.471416;
        
        $location = json_decode((file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='.urlencode($req))));
        if($location->status == 'OK') {
          $lat = $location->results[0]->geometry->location->lat;
          $lon = $location->results[0]->geometry->location->lng;
          $geo = new Geocache;
          $geo->setQ($req);
          $geo->setLongitude($lon);
          $geo->setLatitude($lat);
          $geo->save();
        }
      }
      
      if ($geo) {
        
        /// Distance
        $longitude = $geo->getLongitude();
        $latitude = $geo->getLatitude();
        $distance = 10;
        
        
        if ($longitude > 0 && $latitude > 0) {
          $qs[] = "SET @orig_lat = {$latitude};"; 
          $qs[] = "SET @orig_lon = {$longitude};"; 
          $qs[] = "SET @dist = {$distance};"; 
          $qs[] = <<<EOT
SELECT company.id , 3956 *2 * ASIN( SQRT( POWER( SIN( (
@orig_lat - abs( company.latitude ) ) * pi( ) /180 /2 ) , 2 ) + COS( @orig_lat * pi( ) /180 ) * COS( abs( company.latitude ) * pi( ) /180 ) * POWER( SIN( (
@orig_lon - company.longitude
) * pi( ) /180 /2 ) , 2 ) )
) AS distance
FROM company
HAVING distance < @dist
ORDER BY distance;
EOT;
          $con = Propel::getConnection();
          foreach ($qs as $q) {
            $stmt = $con->prepare($q);
            $stmt->execute();
          }
          while($row = $stmt->fetch()) {
            $validCompanyIds[] = $row['id'];
          }
        }
      }
    }

    // apply geo search if applicable
    if (count($validCompanyIds) > 0) {
      $c->add(LastminutePeer::COMPANY_ID, $validCompanyIds, Criteria::IN);
    }
    
    $cache = pvCache::getCache('lastminutes-facebook');
    if ($cache) {
      $c->add(LastminutePeer::ID, $cache, Criteria::IN);
      $objects = LastminutePeer::doSelectWithI18N($c, 'nl_NL');
    }
    else {
      // limit to facebook saleschannel
      $cx = new Criteria;
      $cx->add(LastminuteSaleschannelPeer::SALESCHANNEL_ID, 2);
      $published = LastminuteSaleschannelPeer::doSelect($cx);
      $valid_ids = array();
      foreach ($published as $p) {
        if ($p->getPublished()) {
          $valid_ids[] = $p->getLastminuteId();
        }
      }
    
      $c->add(LastminutePeer::ID, $valid_ids, Criteria::IN);
     
      $objects = LastminutePeer::doSelectWithI18N($c, 'nl_NL');
      
      foreach ($objects as $key => $object) {
        if (!$object->hasValidTimes()) unset($objects[$key]);
      }

      $cache = array();
      foreach ($objects as $object) {
        $cache[] = $object->getId();
      }
      pvCache::setCache('lastminutes-facebook', $cache);
    }
    
    $perLoad = 5;
    
    if ($this->getRequest()->isXmlHttpRequest()) {
      $this->setLayout(false);
      $showItems = $this->getUser()->getAttribute('showItems');
      $showItems += $perLoad;
      if ($showItems > count($objects)) exit;
      $this->getUser()->setAttribute('showItems', $showItems);
    }
    else {
      $showItems = 0;
      $this->getUser()->setAttribute('showItems', $showItems);
    }
    
    $this->items = $this->getRequestParameter('currentItem') + $perLoad;
   
    $objects = array_slice($objects,$showItems, $perLoad);
    
    $this->objects = $objects;
  
  }
  
  public function executeDetail()
  {
    $c = new Criteria;
    $c->add(RoutePeer::URL, '/lastminutes/'.$this->getRequestParameter('company_name').'/'.$this->getRequestParameter('deal'));
    $object = false;
    $route = RoutePeer::doSelectOne($c);
    $c->clear();
    $object = LastminutePeer::retrieveByPk($route->getObjectId());
    $this->forward404Unless($object);
    $this->object = $object;
    
    $this->setOpenGraphData($object);
  }
  
  public function executeBook()
  {
    $errors = array();
    $message = '';
    
    $consumer = $appointment = $user = false;
    $booking = array();
    
    $object = $this->object;
    
    $this->setOpenGraphData($object);
    
    $paymethod = 'Ideal';
    
    if ($object->getPayideal()) {
      $paymethod = 'Ideal';
    }
    if ($object->getPayoprekening()) {
      $paymethod = 'Oprekening';
    }
    
    $this->paymethod = $paymethod;
      
      
    $this->getRequest()->setParameter('object', $object);

    if ($this->user) {
      $booking['name'] = $this->user->name;
      $booking['email'] = $this->user->email;
    }  
    
    if ($this->getRequest()->hasParameter('logoff')) {
      $this->getUser()->setAttribute('consumer_id', null);
      $this->getUser()->setAttribute('user_id', null);
    }
    
    if ($this->getRequest()->getMethod() == 'POST') {
      
      $booking['name'] = $this->getRequestParameter('name');
      $booking['email'] = $this->getRequestParameter('email');
      
      if ($this->getRequestParameter('name') == '') {
        $errors['name'] = 'Je hebt geen naam ingevoerd.';
      }
      
      if ($this->getRequestParameter('email') == '') {
        $errors['email'] = 'Je hebt geen e-mail adres ingevoerd.';
      }
      
      if ($this->getRequestParameter('issuerId') == '') {
        $errors['issuerId'] = 'Je hebt geen bank geselecteerd.';
      }
        
      if ($this->hasRequestParameter('accept-newsletter')) {
        $booking['newsletter'] = true;
        $booking['newsletter_frequency'] = $this->getRequestParameter('newsletter-frequency');
      }
      
      if ($this->hasRequestParameter('accept-tagletter')) {
        $booking['tagletter'] = true;
      }
      
      if ($this->getRequestParameter('time') == '') {
        $errors['time'] = 'Je hebt geen tijd geselecteerd.';
      }
      
      
      // reset adapter this way, so we have a singleton
      $adapter = CapacityConnector::getAdapter($object->getCompany());
      sfContext::getInstance()->getUser()->setAttribute('fake_company', null);
      if (!CapacityConnector::validateBookingForm($object)) {
        $errors = array_merge($errors, CapacityConnector::getErrors());
      }
      sfContext::getInstance()->getUser()->setAttribute('fake_company', $object->getCompanyId());
      
      if (count($errors) == 0) {
        
        
        if (isset($booking['consumer_id'])) {
          $consumer = ConsumerPeer::retrieveByPk($booking['consumer_id']);
        }
        
        if (!$consumer) {
          $consumer = new Consumer;
          $consumer->setTitle($booking['name']);
          $consumer->setPhone(isset($booking['phone']) ? $booking['phone'] : '');
          $consumer->setEmail($booking['email']);
          $consumer->setCompanyId($object->getCompanyId());
          $parts = explode(' ', $booking['name']);
          if (count($parts) == 1) {
            $firstName = $insertions = '';
            $lastName = $parts[0];
          }
          else if (count($parts) == 2) {
            $firstName = $parts[0];
            $insertions = '';
            $lastName = $parts[1];
          }
          else {
            $firstName = array_shift($parts);
            $lastName = array_pop($parts);
            $insertions = implode(' ', $parts);
          }
          
          $consumer->setFirstname($firstName);
          $consumer->setInsertions($insertions);
          $consumer->setLastname($lastName);
          $consumer->setCountry('NL');
          $consumer->setLanguage('nl');
          $consumer->setStatus('enabled');
          
          if (isset($booking['username']) && isset($booking['password'])) {
            $user = new User;
            $user->setTitle($booking['name']);
            $user->setEmail($booking['email']);
            $user->setUsername($booking['username']);
            $salt = md5(microtime());
            
            $password = hash('sha512', $booking['password'].$salt);
            $user->setSalt($salt);
            $user->setPassword($password);
            $user->save();
            
            $consumer->setUserId($user->getId());
            $consumer->save();
            $booking['consumer_id'] = $consumer->getId();
            $this->getUser()->setAttribute('consumer_account', true);
          }
        }
        else {
          if (isset($booking['username']) && !isset($booking['password'])) {
            $user = new User;
            $user->setTitle($booking['name']);
            $user->setEmail($booking['email']);
            $user->setUsername($booking['username']);
            $user->save();
            
            $consumer->setUserId($user->getId());
            $consumer->save();
            $booking['consumer_id'] = $consumer->getId();
            $this->getUser()->setAttribute('consumer_account', true);
          }
        }
        
        // all mailing related stuff
        $contact = ContactPeer::retrieveFor($consumer, $object->getCompany());
        $contact->setNewsletter(isset($booking['newsletter']) && $booking['newsletter']);
        if (isset($booking['newsletter']) && $booking['newsletter']) {
          $contact->setFrequency($booking['newsletter_frequency']);
        }
        if (isset($booking['tagletter']) && $booking['tagletter']) {
          $contact->setTagletter($object);
          $contact->setTargetmail($this->getRequestParameter('category'));
        }
       
        $contact->save();
  
        
        $consumer->save();
        
        $this->getUser()->setAttribute('consumer_id', $consumer->getId());
        
        $booking['consumer_id'] = $consumer->getId();
        
        $date = $_POST['time'];
        
        $apptype = ApptypePeer::retrieveByPk($this->getRequestParameter('apptype'));

        $duration = $apptype->getDuration();

       // $date = '2012-09-18 20:00';
        //if (!strpos($date, ':')) {
        //  $date = substr($date,0,2).':'.substr($date,2,2);
        //}
        
        $this->getUser()->setAttribute('fake_company', $object->getCompanyId());
        
        $appointment = new Appointment;
        $appointment->setConsumerId($booking['consumer_id']);
        $appointment->setApptypeId($apptype->getId());
        $appointment->setStatus('enabled');
        $appointment->setLabel('aproved');
        $appointment->setTitle($object->getTitle());
        $appointment->setDescription($object->getDescription());
        $appointment->setStarttime($date.':00');
        $appointment->setFinishtime(date('Y-m-d H:i', strtotime('+'.$duration.' minutes', strtotime($date))).':00');
        $appointment->setBlockedtime(date('Y-m-d H:i', strtotime('+'.$duration.' minutes', strtotime($date))).':00');
        $appointment->setCompanyId($object->getCompanyId());
        $appointment->save();
        $booking['appointment_id'] = $appointment->getId();
       
        CapacityConnector::saveBookingForm($object);
        
        $booked = CapacityConnector::bookAppointment($object, array(
          'starttime' => $appointment->getStarttime(),
          'finishtime' => $appointment->getFinishtime(),
          'apptype' => $apptype->getId(),
          'appname' => $object->getTitle(),
          'appdescription' => $object->getDescription(),
          'firstname' => $consumer->getFirstname(),
          'insertions' => $consumer->getInsertions(),
          'lastname' => $consumer->getLastname(),
          'email' => $consumer->getEmail(),
          'phone' => $consumer->getPhone(),
          'appointment_id' => $booking['appointment_id']
        ));
        
        
        if (is_string($booked)) {
          // failed to book
          $appointment->delete();
          unset($booking['appointment_id']);
          sfContext::getInstance()->getUser()->setAttribute('fake_company', null);
          $adapter = CapacityConnector::getAdapter($object->getCompany());
          
          $errors['booking'] = $booked;
        }
        else {
          $this->getUser()->setAttribute('appointment_id', $appointment->getId());
          
          $c = new Criteria;
          $c->add(CapacityupdatePeer::COMPANY_ID, $object->getCompanyId());
          $isUpdating = CapacityupdatePeer::doSelectOne($c);
          if (!$isUpdating) {
            $isUpdating = new Capacityupdate;
            $isUpdating->setCompanyId($object->getCompanyId());
            $isUpdating->save();
          }
          
        }
      
      }
      else if (count($errors) > 0) {
        // always reset the ideal payment if there is any error
        $this->getRequest()->setParameter('issuerId', null);
      }
    }
    
    if (!$consumer && $this->getUser()->hasAttribute('consumer_id')) {
      // we are logged in, so prefill some elements
      
      $consumer = ConsumerPeer::retrieveByPk($this->getUser()->getAttribute('consumer_id'));
      $this->getRequest()->setParameter('name', $consumer->getTitle());
      $this->getRequest()->setParameter('email', $consumer->getEmail());
      $this->getRequest()->setParameter('phone', $consumer->getPhone());
      
      if (!isset($contact)) {
        $contact = ContactPeer::retrieveFor($consumer, $object->getCompany());
      }
      
      if ($contact->getHasmailing()) {
        $this->getRequest()->setParameter('accept-newsletter', true);
      }
      if ($contact->getFrequency() > 0) {
        $this->getRequest()->setParameter('accept-tagletter', true);
        $this->getRequest()->setParameter('newsletter-frequency', $contact->getFrequency());
      }
    }
    
    if ($this->getUser()->hasAttribute('appointment_id')) {
      if (!$appointment) {
        $appointment = AppointmentPeer::retrieveByPk($this->getUser()->getAttribute('appointment_id'));
      }
      
      if ($appointment) {
      
      
        $capacity = (CompanyprofilesettingPeer::findByKey($object, 'availability/type', 'resource')->getValue() == 'capacity');
        if ($capacity) {
          $price = $appointment->getApptype()->getPrice() * $appointment->getApptype()->getCapacity();
        }
        else {
          $price = ($appointment->getApptype()->getPrice() / 100) * (100 - ($object->getMaxdiscount() - $object->getFee()));
        }
        
        $v2 = CompanyprofilesettingPeer::findByKey($object, 'payment/paymentfee', 1.5)->getValue();
        $price += $v2;
        
        $payment = false;
        if ($this->hasRequestParameter('invoicecode')) {
          $payment = ConsumerpaymentPeer::retrieveByInvoiceCode($this->getRequestParameter('invoicecode'));
        }
        
        
        if (!$payment) {
          $payment = new Consumerpayment;
          $payment->setDate(time());
          $payment->setTotal($price);
          $payment->setNormalTotal($appointment->getApptype()->getPrice());
          $payment->setDiscount($object->getMaxdiscount());
          $payment->setBookingfee($v2);
          $payment->setCompanyfee($object->getFee());
          $payment->setDescription('Online betaling PlekjeVrij.nl');
          $payment->setCurrency('EURO');
          $payment->setLastminuteId($object->getId());
          $payment->setStatus = 'open';
          $payment->setAdapter($paymethod);
          $payment->setAppointmentId($this->getUser()->getAttribute('appointment_id'));
          
          $payment->setConsumerInfo($consumer->getTitle().', '.$consumer->getEmail());
          $payment->setLastminuteInfo($object->getTitle().', '.$object->getCompany()->getTitle());
          $payment->setAppointmentInfo($appointment->getApptype()->getDescription().', '.$appointment->getStarttime().' tot '.$appointment->getFinishtime());
        }
        
        if (isset($booking['consumer_id'])) {
          $payment->setConsumerId($booking['consumer_id']);
          $payment->save();
        }
      }
      else {
        // no appointment yet, create a dummy payment to populate the issuer list
        $payment = new Consumerpayment;
        $payment->setTotal(1);
        $payment->setDescription('Testbetaling');
      }
    }
    else {
      // no appointment yet, create a dummy payment to populate the issuer list
      $payment = new Consumerpayment;
      $payment->setTotal(1);
      $payment->setDescription('Testbetaling');
    }
    
    $return_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'/bedankt';
    if (!$this->getUser()->hasAttribute('return_url')) {
      $this->getUser()->setAttribute('return_url', $return_url);
    }
    $payments = new Payments;
    $payments->prepareTransaction($paymethod, $payment);
    $paymentForm = $payments->doTransaction();
    $this->paymentForm = $paymentForm;
    
    $status = $payment->getStatus();
 
    if ($status != 'open' && $status != '') {
      
      $appointmentId = $this->getUser()->getAttribute('appointment_id');
      $appointment = AppointmentPeer::retrieveByPk($appointmentId);
      
      $this->getUser()->setAttribute('appointment_id', null);
      if ($status == 'success') {

        if($this->getUser()->hasAttribute('targetmail_lead')) {
          
          $contactmailing_contact = ContactmailingContactPeer::retrieveByPk($this->getUser()->getAttribute('targetmail_lead'));
          if ($contactmailing_contact) {
            
            $link = $this->getUser()->getAttribute('targetmail_lead');
            
            $contactmailing = $contactmailing_contact->getContactmailing();
    
            $buyed = array();
            if ($contactmailing_contact->getBuyed() != '') {
              $buyed = unserialize($contactmailing_contact->getBuyed());
            }
            
            $totalbuyed = array();
            if ($contactmailing->getBuyed() != '') {
              $totalbuyed = unserialize($contactmailing->getBuyed());
            }
            
            if (!in_array($link, $buyed)) {
              $buyed[] = $link;
              if (!isset($totalbuyed[$link])) {
                $totalbuyed[$link] = 0;
              }
              $totalbuyed[$link]++;
              
              $contactmailing_contact->setBuyed(serialize($buyed));
              $contactmailing_contact->save();
              
              $contactmailing->setBuyed(serialize($totalbuyed));
              $contactmailing->save();
            }
          }
          
          $sf_user->setAttribute('targetmail_lead', null);
          $sf_user->setAttribute('targetmail_link', null);
        }
        
        sfContext::getInstance()->getConfiguration()->loadHelpers('ZeusRoute');
        $this->redirect(route_for($object).'/reserveren/bedankt');
      }
      else {
        $this->getUser()->setAttribute('fake_company', $object->getCompanyId());
        CapacityConnector::cleanupAppointment($object, $appointment);
        $errors['payment'] = 'De betaling is niet gelukt.';
      }
    }
    
    $this->booking = $booking;
    $this->errors = $errors;
    $this->message = $message;
    
  }
  
  public function executeThankyou()
  {
  }
  
  public function setOpenGraphData($object) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
    $app = zeusYaml::load(sfConfig::get('sf_app_dir'). '/config/app.yml');
    $cfg = $app['all']['facebook'];
    
    $cfg['appid'] = '357145671030550';
    slot('opengraph');
    ?>
<meta property="fb:app_id" content="<?php echo $cfg['appid']; ?>" /> 
<meta property="og:type" content="website" /> 
<meta property="og:determiner" content="the" /> 
<meta property="og:title" content="<?php echo $object->getTitle(); ?>" /> 
<meta property="og:image" content="http://<?php echo $_SERVER['SERVER_NAME']; ?><?php echo $object->getMainImage(); ?>" /> 
<meta property="og:description" content="<?php echo zeusTools::smartText(strip_tags($object->getDescription()), 512); ?>" /> 
<meta property="lastminutes:category" content="<?php echo $object->getCategoryNames(); ?>" />
<?php
    end_slot();
  }
}