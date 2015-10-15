<?php

class adminActions extends sfActions
{
  public function preExecute()
  {
    $this->getUser()->setAttribute('crumblepath', array());
    $this->getUser()->setAttribute('subnav', array());
    $this->getUser()->setAttribute('buttons', array());
  }

  public function executePlanboard()
  {
    $this->getResponse()->addJavascript('/js/planboard.js', 'last');

    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(ResourcePeer::COMPANY_ID, $credentials->getCompanyId());
    $resources = ResourcePeer::doSelect($c);
    $team_resources = array();
    $teams = array();
    foreach ($resources as $resource) {
      if (!isset($team_resources[$resource->getTeamId()])) {
        $team_resources[$resource->getTeamId()] = array();
        $teams[] = $resource->getTeam();
      }
      $team_resources[$resource->getTeamId()][] = $resource;
    }

    $this->teams = $teams;
    $this->team_resources = $team_resources;
/*
    $oaapi = new OAAPI;
    $response = $oaapi->sendRequest('getAgendas');
    if ($response) {
      $agenda_id = $response['Agenda'][0]['Id'];
*/
            // load customers
/*
            $customers = $oaapi->sendRequest('getCustomers', array('Limit' => 1, 'Offset' => 0));

            $offset = 0;
            $limit = 500;
            $data = array();
            while ($offset < (string)$customers['Stats']->TotalRecords) {
              $customers = $oaapi->sendRequest('getCustomers', array('Limit' => $limit, 'Offset' => $offset));
              $data = array_merge($data, $customers['Customer']);
              $offset += $limit;
            }

            foreach ($data as $customer_oa) {
              $c->clear();
              $c->add(CustomerPeer::OA_CUSTOMER_ID, $customer_oa['Id']);
              $customer = CustomerPeer::doSelectOne($c);
              if (!$customer) {
                $customer = new Customer;
                $customer->setOaCustomerId($customer_oa['Id']);
                $address = new Address;
                $address->save();
                $customer->setAddressId($address->getId());
              }
              else {
                $address = $customer->getAddress();
              }

              $customer->setCompanyId($credentials->getCompanyId());
              $customer->setTitle(str_replace('  ', ' ', $customer_oa['FirstName'].' '.$customer_oa['Insertions'].' '.$customer_oa['LastName']));
              $customer->setEmail($customer_oa['Email']);
              $customer->setPhone($customer_oa['Phone']);
              $customer->save();

              $address->setAddress(str_replace('  ', ' ', $customer_oa['Street'].' '.$customer_oa['HouseNr'].' '.$customer_oa['HouseNrAddition']));
              $address->setZipcode($customer_oa['ZipCode']);
              $address->setCity($customer_oa['City']);
              $address->setCountry('nl');
              $address->save();


            }
            echo count($data).' customers imported.';
            exit;

*/
      // load appointments
/*
      $colorMap = array();

            foreach ($resources as $resource) {
              if($resource->getOaResourceId() > 0) {
                $resource_id = $resource->getOaResourceId();

                $response = $oaapi->sendRequest('getAppointments', array(
                  'AgendaId' => $agenda_id,
                  'StartDate' => '2015-10-01',
                  'EndDate' => '2015-10-31',
                  'ResourceId' => $resource_id
                ));
                if ($response) {
                  $appointments = $response['Appointment'];
                  foreach($appointments as $appointment) {
                    $c->clear();
                    $c->add(AppointmentPeer::OA_APPOINTMENT_ID, $appointment['Id']);
                    $local_app = AppointmentPeer::doSelectOne($c);
                    if (!$local_app) {
                      $local_app = new Appointment;
                      $local_app->setOaAppointmentId($appointment['Id']);
                    }
                    if (trim($appointment['Description']) != '') {
                      $local_app->setTitle($appointment['Name'].': '.trim($appointment['Description']));
                    } else {
                      $local_app->setTitle($appointment['Name']);
                    }

                    if (!in_array($appointment['AppointmentTypeId'], $colorMap)) {
                      $colorMap[] = $appointment['AppointmentTypeId'];
                    }
                    $color = array_search($appointment['AppointmentTypeId'], $colorMap);
                    $local_app->setColor($color);

                    $c->clear();
                    $c->add(CustomerPeer::OA_CUSTOMER_ID, $appointment['CustomerId']);
                    $customer = CustomerPeer::doSelectOne($c);

                    $local_app->setDate($appointment['StartTime']);
                    $local_app->setEndDate($appointment['FinishTime']);
                    //$local_app->setWorkorderId();
                    $local_app->setResourceId($resource->getId());
                    if ($customer) {
                      $local_app->setCustomerId($customer->getId());
                      $local_app->setAddressId($customer->getAddressId());
                    }

                    $local_app->setDuration((strtotime($appointment['FinishTime']) - strtotime($appointment['StartTime'])) / 60);
                    $local_app->save();
                  }
                }
              }
            }
    }
*/

    $c->clear();
    $resource_ids = array();
    foreach ($resources as $resource) {
      if ($resource->getOaResourceId() > 0) {
        $resource_ids[] = $resource->getId();
      }
    }

    $c->add(AppointmentPeer::RESOURCE_ID, $resource_ids, Criteria::IN);
    $c->add(AppointmentPeer::DATE, date('Y-m-01'), Criteria::GREATER_EQUAL);
    $c->add(AppointmentPeer::DATE, date('Y-m-d', strtotime('+1 month', strtotime(date('Y-m-01')))), Criteria::LESS_EQUAL);
    $appointments = AppointmentPeer::doSelect($c);
    $this->appointments = $appointments;
    $this->resource_map = $resource_ids;

    $this->getUser()->setAttribute('crumblepath', array(
      'admin/planboard' => 'planbord',
      'vandaag'
    ));

    $this->getUser()->setAttribute('buttons', array(
      //array('label' => 'Opslaan', 'action' => "alert('test');"),
      //array('label' => 'Nog een knop', 'action' => "alert('test 2');", 'type' => 'submit'),
      //array('label' => 'Class 2', 'action' => "alert('test 2');", 'class' => 'button-2'),
      //array('label' => 'Class 3', 'action' => "alert('test 2');", 'class' => 'button-3'),
    ));

    $this->getUser()->setAttribute('subnav', array(
        array(
          'title' => 'Planning',
          'items' => array(
            'Planboard.new();' => 'Nieuwe afspraak',
            'Planboard.listView();' => array('list' => 'Lijstweergave'),
            'Planboard.gridView();' => array('grid' => 'Strokenplanner'),
            'Planboard.mapView();' => array('map' => 'Strokenplanner met kaart')
          )
        ),
        array(
          'title' => 'Datum',
          'items' => array(
            '<div id="date-picker" style="width:100%;">',
          )
        ),
        array(
          'title' => 'Niet ingepland',
          'items' => array(
            //'<div id="app-0" class="appointment"><strong>Reparatie<br>2 uur</strong><br>Mevr.Obdam<br>Richard Holstraat 35<br>2324VH Leiden</div>',
            //'<div id="app-1" class="appointment"><strong>Service ketel<br>1 uur</strong><br>Fam. Hoek<br>Jan Evertenlaan 45<br>2312EE Voorschoten</div>',
            'hook' => 'hook',
            '<div id="dropzone">Sleep een afspraak hierheen om deze van de planning te halen</div>'
          )
        )
      )
    );
  }

  public function executePlanboardAjax()
  {
    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(ResourcePeer::COMPANY_ID, $credentials->getCompanyId());
    $resources = ResourcePeer::doSelect($c);
    $resource_ids = array();
    $resource_objects = array();
    foreach ($resources as $resource) {
      $resource_ids[] = $resource->getId();
      $resource_objects[$resource->getId()] = $resource;
    }
    $c->clear();
    $c->add(AppointmentPeer::RESOURCE_ID, $resource_ids, Criteria::IN);
    $total = AppointmentPeer::doCount($c);

    $offset = $this->hasRequestParameter('offset') ? (int)$this->getRequestParameter('offset') : 0;
    $c->setOffset($offset);
    $c->setLimit(250);
    $appointments = AppointmentPeer::doSelect($c);
    $data = array();
    foreach ($appointments as $appointment) {
      $data[] = array(
        $appointment->getId(),
        date('d-m-Y', strtotime($appointment->getDate())),
        date('H:i', strtotime($appointment->getDate())),
        date('H:i', strtotime($appointment->getEndDate())),
        $resource_objects[$appointment->getResourceId()] ? $resource_objects[$appointment->getResourceId()]->getName() : "Verwijderde medewerker",
        $appointment->getCustomer() ? $appointment->getCustomer()->getFullName() : "",
        $appointment->getAddress() ? $appointment->getAddress()->getFullAddress() : "",
      );
    }
    $this->data = $data;
    header('Content-type: application/json');

    echo json_encode(array('data' => $data, 'offset' => $offset, 'limit' => 100, 'total' => $total));
    exit;
  }

  public function executeWorkorders()
  {
    /*
    $data = array(
      array(
        'd' => 'Arbeidstijd',
        't' => 'product',
        'p' => 50,
        'c' => 60
      ),
      array(
        'd' => 'CV ketel (solo) tot 40KW',
        't' => 'product',
        'p' => 74,50,
        'c' => 1
      ),
    );
    echo json_encode($data);
    exit;
    */

    if ($this->hasRequestParameter('download')) {
      $credentials_id = $this->getUser()->getAttribute('userid');
      $credentials = CredentialsPeer::retrieveByPk($credentials_id);
      $this->forward404Unless($credentials);

      $c = new Criteria;
      $c->add(WorkorderPeer::COMPANY_ID, $credentials->getCompanyId());
      $c->add(WorkorderPeer::ID, $this->getRequestParameter('download'));
      $workorder = WorkorderPeer::doSelectOne($c);
      $this->forward404Unless($workorder);

      $c->clear();
      $c->add(InvoicePeer::WORKORDER_ID, $workorder->getId());
      $c->add(InvoicePeer::COMPANY_ID, $credentials->getCompanyId());
      $invoice = InvoicePeer::doSelectOne($c);
      $this->forward404Unless($invoice);

      header('Content-type: application/pdf');
      header("Content-Disposition:attachment;filename=factuur-".basename($invoice->getPdf()));
      echo file_get_contents(sfConfig::get('sf_web_dir').$invoice->getPdf());
      exit;
    }
    $this->getResponse()->addJavascript('/js/list.js', 'last');

    $this->getUser()->setAttribute('crumblepath', array('werkbonnen'));

    $this->setLayout('layout-wide');
    /*
    $this->getUser()->setAttribute('subnav', array(
      array(
        'title' => 'Werkbonnen',
        'items' => array(
          'workorder.new();' => 'Nieuwe werkbon'
        )
      ),
      array(
        'title' => 'Datum',
        'items' => array(
          //'<div id="date-picker" style="width:100%;">',
        )
      )
    ));
    */
  }

  public function executeWorkordersAjax()
  {
    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(WorkorderPeer::COMPANY_ID, $credentials->getCompanyId());
    $total = WorkorderPeer::doCount($c);

    $offset = $this->hasRequestParameter('offset') ? (int)$this->getRequestParameter('offset') : 0;
    $c->setOffset($offset);
    $c->setLimit(250);
    $workorders = WorkorderPeer::doSelect($c);
    $data = array();
    foreach ($workorders as $workorder) {
      $data[] = array(
        $workorder->getId(),
        $workorder->getCustomer()->getFullName(),
        $workorder->getAddress()->getFullAddress(),
        // $workorder->getStatus(),
        date('d-m-Y', strtotime($workorder->getDate())),
        $workorder->getResource()->getName(),
        $workorder->getRemarks(),
        $workorder->getReady() ? 'Ja' : 'Nee'
      );
    }
    $this->data = $data;
    header('Content-type: application/json');

    echo json_encode(array('data' => $data, 'offset' => $offset, 'limit' => 100, 'total' => $total));
    exit;
  }

  public function executeWorkordersData()
  {
    header('Content-type: application/json');

    /*
     *
     *
     * {
            $data['workorders'][] = array(
              'status' => $workorder->getStatus(),
              'date' => date('Y-m-d', strtotime($workorder->getDate())),
              'ready' => (bool)$workorder->getReady(),
              'id' => $workorder->getId()
            );

            // get the images for this workorder
            $c->clear();
            $c->add(FilePeer::FTYPE, 'image');
            $c->add(FilePeer::WORKORDER_ID, $workorder->getId());
            $images = FilePeer::doSelect($c);
            foreach ($images as $image) {
              $data['photos'][] = array(
                'date' =>  date('Y-m-d', strtotime($image->getDate())),
                'path' => zeusImages::getPresentation($image->getPath(), array('width' => 800, 'height' => 600, 'resize_method' => zeusImages::RESIZE_CHOP)),
                'thumb' => zeusImages::getPresentation($image->getPath(), array('width' => 160, 'height' => 100, 'resize_method' => zeusImages::RESIZE_CHOP))
              );
            }
          }
          foreach ($invoices as $invoice) {
            $data['invoices'][] = array(
              'status' => $invoice->getStatus(),
              'date' => date('Y-m-d', strtotime($invoice->getDate())),
              'rows' => json_decode($invoice->getOrderrows()),
              'id' => $invoice->getId()
            );
          }
     */
    $id = $this->getRequestParameter('id');

    $data = array('status' => 'failure');

    $method = $this->hasRequestParameter('method') ? $this->getRequestParameter('method') : 'load';
    $workorder = WorkorderPeer::retrieveByPk($id);
    if ($workorder) {
      switch ($method) {
        case 'load':
          $data = array(
            'id' => $id,
            'status' => $workorder->getStatus(),
            'date' => date('d-m-Y', strtotime($workorder->getDate())),
            'remarks' => $workorder->getRemarks(),
            'ready' => $workorder->getReady() ? 'Ja' : 'Nee',
            'customer_id' => $workorder->getCustomer()->getId(),
            'resource_id' => $workorder->getResourceId(),
            'resource_name' => $workorder->getResource()->getName(),
            'name' => $workorder->getCustomer()->getTitle(),
            'email' => $workorder->getCustomer()->getEmail(),
            'phone' => $workorder->getCustomer()->getPhone(),
            'address' => $workorder->getAddress()->getAddress(),
            'zipcode' => $workorder->getAddress()->getZipcode(),
            'city' => $workorder->getAddress()->getCity(),
            'country' => $workorder->getAddress()->getCountry(),
            'photos' => array(),
            'invoices' => array(),
            'payments' => array(),
            'signature' => '<img src="'.$workorder->getSignature().'">',
            'orderrows' => json_decode($workorder->getOrderrows())
          );

          // get the images for this workorder
          $c = new Criteria;
          $c->add(FilePeer::FTYPE, 'image');
          $c->add(FilePeer::WORKORDER_ID, $workorder->getId());
          $images = FilePeer::doSelect($c);
          foreach ($images as $image) {
            $data['photos'][] = array(
              'date' =>  date('Y-m-d', strtotime($image->getDate())),
              'path' => zeusImages::getPresentation($image->getPath(), array('width' => 800, 'height' => 600, 'resize_method' => zeusImages::RESIZE_CHOP)),
              'thumb' => zeusImages::getPresentation($image->getPath(), array('width' => 160, 'height' => 100, 'resize_method' => zeusImages::RESIZE_CHOP))
            );
          }

          // get the invoice for this workorder
          $c->clear();
          $c->add(InvoicePeer::WORKORDER_ID, $workorder->getId());
          $invoice = InvoicePeer::doSelectOne($c);
          if ($invoice) {
            $data['invoices'][] = array(
              //'status' => $invoice->getStatus(),
              'date' => date('d-m-Y', strtotime($invoice->getDate())),
              'total' => '€ '.number_format($invoice->getTotal(), 2, ',', '.'),
              'totalv' => number_format($invoice->getTotal(), 2, ',', ''),
              'rows' => json_decode($invoice->getOrderrows()),
              'id' => $invoice->getId()
            );
          }

          // get the payment for this workorder
          if ($invoice) {
            $c->clear();
            $c->add(PaymentPeer::INVOICE_ID, $invoice->getId());
            $payment = PaymentPeer::doSelectOne($c);
            if ($payment) {
              $data['payments'][] = array(
                'status' => $payment->getStatus(),
                'date' => date('d-m-Y', strtotime($invoice->getDate())),
                'paymethod' => $payment->getPaymethodStr(),
                'total' => '€ '.number_format($payment->getTotal(), 2, ',', '.'),
                'totalv' => number_format($payment->getTotal(), 2, ',', ''),
                'id' => $payment->getId()
              );
            }
          }

          $data['status'] = 'success';
          break;

        case 'save':
          $workorder->setStatus($this->getRequestParameter('status'));
          $workorder->setDate($this->getRequestParameter('date'));
          $workorder->setRemarks($this->getRequestParameter('remarks'));
          $workorder->setReady($this->getRequestParameter('ready'));
          $workorder->save();

          $data['status'] = 'success';
          break;

        case 'delete':
          $data['id'] = $workorder->getId();
          $workorder->delete();
          if ($workorder->isDeleted()) {
            $data['status'] = 'success';
          }
          else {
            $data['status'] = 'failure';
          }

      }
    }
    echo json_encode($data);
    exit;
  }

  public function executeCustomers()
  {
    $this->getUser()->setAttribute('crumblepath', array('klanten'));
    /*
        $fp = fopen(getcwd().'/../export_klanten.csv', 'r');
        while ($row = fgetcsv($fp, 2048, ";", '"')) {
          var_dump($row);
          $customer = new Customer;
          $customer->setCompanyId(2);
          $customer->setTitle(str_replace('  ', ' ', $row[1].' '.$row[2].' '.$row[3]));
          $customer->setEmail($row[15]);
          $customer->setPhone(trim($row[13])==''?$row[14]:$row[13]);

          $address = new Address;
          $address->setAddress(str_replace('  ', ' ', $row[7].' '.$row[8].' '.$row[9]));
          $address->setZipcode($row[10]);
          $address->setCity($row[11]);
          $address->setCountry('nl');
          $address->save();
          $customer->setAddressId($address->getId());
          $customer->save();

          // merk $row[18]
          // type $row[19]
        }
        fclose($fp);
        exit;
    */
    $data = array();
    $this->data = $data;

    $this->setLayout('layout-wide');
    /*
    $this->getUser()->setAttribute('subnav', array(
      array(
        'title' => 'Klanten',
        'items' => array(
          'Customer.new();' => 'Nieuwe klant'
        )
      )
    ));*/
  }

  public function executeCustomersData()
  {
    header('Content-type: application/json');

    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $id = $this->getRequestParameter('id');

    $data = array('status' => 'failure');

    $method = $this->hasRequestParameter('method') ? $this->getRequestParameter('method') : 'load';
    $form = $this->hasRequestParameter('form') ? $this->getRequestParameter('form') : 'customer';
    switch($form) {
      case 'customer':

        $customer = CustomerPeer::retrieveByPk($id);
        if ($customer) {
          switch ($method) {
            case 'load':
              $address = $customer->getAddress();

              $c = new Criteria;
              $c->add(WorkorderPeer::CUSTOMER_ID, $id);
              $workorders = WorkorderPeer::doSelect($c);

              $c->clear();
              $c->add(InvoicePeer::CUSTOMER_ID, $id);
              $invoices = InvoicePeer::doSelect($c);

              $c->clear();
              $c->add(NotePeer::CUSTOMER_ID, $id);
              $notes = NotePeer::doSelect($c);

              $data = array(
                'id' => $id,
                'title' => $customer->getTitle(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getPhone(),
                'address' => $address->getAddress(),
                'zipcode' => $address->getZipcode(),
                'city' => $address->getCity(),
                'country' => $address->getCountry(),
                'workorders' => array(),
                'invoices' => array(),
                'notes' => array(),
                'photos' => array()
              );

              foreach ($workorders as $workorder) {
                $data['workorders'][] = array(
                  'status' => $workorder->getStatusStr(),
                  'date' => date('Y-m-d', strtotime($workorder->getDate())),
                  'ready' => (bool)$workorder->getReady(),
                  'id' => $workorder->getId()
                );

                // get the images for this workorder
                $c->clear();
                $c->add(FilePeer::FTYPE, 'image');
                $c->add(FilePeer::WORKORDER_ID, $workorder->getId());
                $images = FilePeer::doSelect($c);
                foreach ($images as $image) {
                  $data['photos'][] = array(
                    'date' => date('Y-m-d', strtotime($image->getDate())),
                    'path' => zeusImages::getPresentation($image->getPath(), array('width' => 800, 'height' => 600, 'resize_method' => zeusImages::RESIZE_CHOP)),
                    'thumb' => zeusImages::getPresentation($image->getPath(), array('width' => 160, 'height' => 100, 'resize_method' => zeusImages::RESIZE_CHOP))
                  );
                }
              }
              foreach ($invoices as $invoice) {
                $data['invoices'][] = array(
                  'status' => $invoice->getStatusStr(),
                  'total' => '€ ' . number_format($invoice->getTotal(), 2, ',', '.'),
                  'date' => date('Y-m-d', strtotime($invoice->getDate())),
                  'rows' => json_decode($invoice->getOrderrows()),
                  'id' => $invoice->getId()
                );
              }

              foreach ($notes as $note) {
                $data['notes'][] = array(
                  'note' => $note->getNote(),
                  'date' => date('Y-m-d', strtotime($note->getDate())),
                  'id' => $note->getId()
                );
              }
              $data['status'] = 'success';
              break;

            case 'save':
              $errors = array();

              if (!$this->validate('title', 'required')) {
                $errors['customer-title'] = 'Naam is een verplicht veld.';
              }
              if ($this->validate('zipcode', 'required')) {
                if (!$this->validate('zipcode', 'zipcode')) {
                  $errors['customer-zipcode'] = 'Postcode is niet in het juiste formaat. Het geldige formaat is 1234AA.';
                }
              }
              if ($this->validate('email', 'required')) {
                if (!$this->validate('email', 'email')) {
                  $errors['customer-email'] = 'Het e-mail adres is niet geldig.';
                }
              }

              if (count($errors) == 0) {
                $customer->setTitle($this->getRequestParameter('title'));
                $customer->setEmail($this->getRequestParameter('email'));
                $customer->setPhone($this->getRequestParameter('phone'));
                $customer->save();

                $address = $customer->getAddress();
                if (!$address) {
                  $address = new Address;
                  $address->setCustomerId($customer->getId());
                }
                $address->setAddress($this->getRequestParameter('address'));
                $address->setZipcode($this->getRequestParameter('zipcode'));
                $address->setCity($this->getRequestParameter('city'));
                $address->save();

                $data['status'] = 'success';
              } else {
                $data['status'] = 'failure';
                $data['errors'] = $errors;
              }

              break;

            case 'delete':
              $data['id'] = $customer->getId();
              $customer->delete();
              if ($customer->isDeleted()) {
                $data['status'] = 'success';
              } else {
                $data['status'] = 'failure';
              }

          }
        }
        break;
      case 'note':
        $note = NotePeer::retrieveByPK($this->getRequestParameter('id'));
        if (!$note) {
          $note = new Note;
          $note->setCompanyId($credentials->getCompanyId());
          $note->setCustomerId($this->getRequestParameter('customer_id'));
        }
        if ($note) {

          switch ($method) {
            case 'load':
              $data['date'] = date('d-m-Y', strtotime($note->getDate()));
              $data['text'] = $note->getNote();
              break;
            case 'save':
              $errors = array();

              if (!$this->validate('text', 'required')) {
                $errors['note-total'] = 'Bedrag is een verplicht veld.';
              }

              if (!$this->validate('date', 'required')) {
                $errors['note-date'] = 'Datum is een verplicht veld.';
              }

              if (count($errors) == 0) {
                $note->setDate(date('Y-m-d', strtotime($this->getRequestParameter('date'))));
                $note->setNote($this->getRequestParameter('text'));
                $note->save();

                $data['status'] = 'success';
              }
              else {
                $data['status'] = 'failure';
                $data['errors']= $errors;
              }

              break;
            case 'delete':
              $data['id'] = $note->getId();
              $note->delete();
              if ($note->isDeleted()) {
                $data['status'] = 'success';
              } else {
                $data['status'] = 'failure2';
              }
              break;
          }
        }
        break;

      case 'photo':
        $c = new Criteria;
        $c->add(WorkorderPeer::CUSTOMER_ID, $this->getRequestParameter('customer_id'));
        $workorders = WorkorderPeer::doSelect($c);

        $images = array();

        foreach ($workorders as $workorder) {
          // get the images for this workorder
          $c->clear();
          $c->add(FilePeer::FTYPE, 'image');
          $c->add(FilePeer::WORKORDER_ID, $workorder->getId());
          $images = FilePeer::doSelect($c);
          foreach ($images as $image) {
            $data['photos'][] = array(
              'date' => date('Y-m-d', strtotime($image->getDate())),
              'path' => zeusImages::getPresentation($image->getPath(), array('width' => 800, 'height' => 600, 'resize_method' => zeusImages::RESIZE_CHOP)),
              'thumb' => zeusImages::getPresentation($image->getPath(), array('width' => 160, 'height' => 100, 'resize_method' => zeusImages::RESIZE_CHOP))
            );
          }
        }

        var_dump($images);

        switch($method) {
          case 'delete':
            $file = $images[$this->getRequestParameter('id')];
            $photo = $data['photos'][$this->getRequestParameter('id')];
            unlink(sfConfig::get('sf_web_dir').$file->getPath());
            unlink(sfConfig::get('sf_web_dir').$photo['path']);
            unlink(sfConfig::get('sf_web_dir').$photo['thumb']);
            unset($data['photos'][$this->getRequestParameter('id')]);
            $file->delete();
            $data['status'] = 'success';
            break;
        }
        break;

      case 'search':
        switch($method) {
          case 'customer':
          case  'zipcode':
            $c = new Criteria;
            if ($method == 'customer') {
              $c->add(CustomerPeer::TITLE, '%'.$this->getRequestParameter('value').'%', Criteria::LIKE);
              $c->setLimit(50);
            }
            else {
              $c2 = new Criteria;
              $c2->add(AddressPeer::ZIPCODE, '%'.$this->getRequestParameter('value').'%', Criteria::LIKE);
              $c2->setLimit(50);
              $addresses = AddressPeer::doSelect($c2);
              $address_ids = array();
              foreach ($addresses as $address) {
                $address_ids[] = $address->getId();
              }
              $c->add(CustomerPeer::ADDRESS_ID, $address_ids, Criteria::IN);
            }
            $customers = CustomerPeer::doSelect($c);
            foreach ($customers as $customer) {
              $data[] = array(
                'title' => $customer->getTitle().' ( '.$customer->getAddress()->getAddress().')',
                'data' => array(
                  'appointment-customer-id' => $customer->getId(),
                  'appointment-ctitle' => $customer->getTitle(),
                  'appointment-zipcode' => $customer->getAddress()->getZipcode(),
                  'appointment-address' => $customer->getAddress()->getAddress(),
                  'appointment-city' => $customer->getAddress()->getCity()
                )
              );
            }
            $data['status'] = 'success';
            break;
        }
    }
    echo json_encode($data);
    exit;
  }

  public function executeCustomersAjax()
  {
    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(CustomerPeer::COMPANY_ID, $credentials->getCompanyId());
    $total = CustomerPeer::doCount($c);

    $offset = $this->hasRequestParameter('offset') ? (int)$this->getRequestParameter('offset') : 0;
    $c->setOffset($offset);
    $c->setLimit(250);
    $customers = CustomerPeer::doSelect($c);
    $data = array();
    foreach ($customers as $customer) {
      $data[] = array(
        $customer->getId(),
        $customer->getTitle(),
        $customer->getAddress()->getAddress(),
        $customer->getAddress()->getZipcode(),
        $customer->getAddress()->getCity(),
        strlen($customer->getEmail()) > 20 ? substr($customer->getEmail(),0,20).'..' : $customer->getEmail(),
        $customer->getPhone()
      );
    }
    $this->data = $data;
    header('Content-type: application/json');

    echo json_encode(array('data' => $data, 'offset' => $offset, 'limit' => 100, 'total' => $total));
    exit;
  }

  public function executeAdmin()
  {
    $this->getUser()->setAttribute('crumblepath', array('administratie'));

    $this->setLayout('layout-wide');
    /*
    $this->getUser()->setAttribute('subnav', array(
      array(
        'title' => 'Rapportage',
        'items' => array(
          'Admin.reportTurnover();' => 'Omzet'
        )
      )
    ));*/
  }

  public function executeAdminAjax()
  {
    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(InvoicePeer::COMPANY_ID, $credentials->getCompanyId());
    $total = InvoicePeer::doCount($c);

    $offset = $this->hasRequestParameter('offset') ? (int)$this->getRequestParameter('offset') : 0;
    $c->setOffset($offset);
    $c->setLimit(250);
    $invoices = InvoicePeer::doSelect($c);
    $data = array();
    foreach ($invoices as $invoice) {
      $data[] = array(
        $invoice->getId(),
        date('d-m-Y', strtotime($invoice->getDate())),
        '€ '.number_format($invoice->getTotal(), 2, ',', '.'),
        $invoice->getStatusStr()

      );
    }
    $this->data = $data;
    header('Content-type: application/json');

    echo json_encode(array('data' => $data, 'offset' => $offset, 'limit' => 100, 'total' => $total));
    exit;
  }

  public function executeAdminData()
  {
    header('Content-type: application/json');

    $id = $this->getRequestParameter('id');

    $data = array('status' => 'failure');

    $method = $this->hasRequestParameter('method') ? $this->getRequestParameter('method') : 'load';
    $form = $this->hasRequestParameter('form') ? $this->getRequestParameter('form') : 'invoice';

    switch($form) {
      case 'invoice':
        $invoice = InvoicePeer::retrieveByPk($id);
        if ($invoice) {
          switch ($method) {
            case 'load':
              $data = array(
                'id' => $id,
                'status' => $invoice->getStatus(),
                'no' => $invoice->getNo(),
                'statusstr' => $invoice->getStatusStr(),
                'total' => '€ ' . number_format($invoice->getTotal(), 2, ',', '.'),
                'date' => date('d-m-Y', strtotime($invoice->getDate())),
                'orderrows' => array(),
                'payments' => array(),
                'title' => $invoice->getCustomer()->getTitle(),
                'address' => $invoice->getAddress()->getAddress(),
                'zipcode' => $invoice->getAddress()->getZipcode(),
                'city' => $invoice->getAddress()->getCity()
              );
              $orderrows = json_decode($invoice->getOrderrows(), true);
              foreach ($orderrows as $orderrow) {
                $data['orderrows'][] = array(
                  'description' => $orderrow['d'],
                  'price' => '€ ' . number_format($orderrow['p'], 2, ',', '.'),
                  'amount' => $orderrow['c'],
                  'total' => '€ ' . number_format($orderrow['p'] * $orderrow['c'], 2, ',', '.')
                );
              }

              $c = new Criteria;
              $c->add(PaymentPeer::INVOICE_ID, $invoice->getId());
              $payments = PaymentPeer::doSelect($c);
              foreach ($payments as $payment) {
                $data['payments'][] = array(
                  'id' => $payment->getId(),
                  'paymethod' => $payment->getPaymethodStr(),
                  'total' => '€ ' . number_format($payment->getTotal(), 2, ',', '.'),
                  'date' => date('d-m-Y', strtotime($payment->getDate()))
                );
              }
              break;

            case 'save':
              $invoice->setStatus($this->getRequestParameter('status'));
              $invoice->setDate($this->getRequestParameter('date'));
              $invoice->save();

              $data['status'] = 'success';
              break;

            case 'delete':
              $data['id'] = $invoice->getId();
              $invoice->delete();
              if ($invoice->isDeleted()) {
                $data['status'] = 'success';
              } else {
                $data['status'] = 'failure';
              }
              break;

          }
        }
        break;

      case 'payment':
        $payment = PaymentPeer::retrieveByPK($this->getRequestParameter('id'));
        if (!$payment) {
          $payment = new Payment;
          $payment->setInvoiceId($this->getRequestParameter('invoice_id'));
        }
        if ($payment) {

          switch ($method) {
            case 'load':
              $data['total'] = $payment->getTotal();
              $data['date'] = date('d-m-Y', strtotime($payment->getDate()));
              $data['paymethod'] = $payment->getPaymethod();
              break;
            case 'save':
              $errors = array();

              if (!$this->validate('total', 'required')) {
                $errors['payment-total'] = 'Bedrag is een verplicht veld.';
              }

              if (!$this->validate('date', 'required')) {
                $errors['payment-date'] = 'Datum is een verplicht veld.';
              }

              if (count($errors) == 0) {
                $payment->setTotal($this->getRequestParameter('total'));
                $payment->setDate(date('Y-m-d', strtotime($this->getRequestParameter('date'))));
                $payment->setPaymethod($this->getRequestParameter('paymethod'));
                $payment->save();

                $data['status'] = 'success';
              }
              else {
                $data['status'] = 'failure';
                $data['errors']= $errors;
              }

              break;
            case 'delete':
              $data['id'] = $payment->getId();
              $payment->delete();
              if ($payment->isDeleted()) {
                $data['status'] = 'success';
              } else {
                $data['status'] = 'failure';
              }
              break;
          }
        }
        break;
    }


    echo json_encode($data);
    exit;
  }

  public function executeSettings()
  {
    $this->getUser()->setAttribute('crumblepath', array('instellingen'));

    $products = array(
      array(
        'title' => 'Diensten',
        'items' => array(
          array(
            'title' => '010 Onderhoud',
            'items' => array(
              array(
                'title' => 'CV ketel (solo) tot 40KW',
                'price' => 74.5
              ),
              array(
                'title' => 'CV ketel (combi) tot 40KW',
                'price' => 90
              ),
              array(
                'title' => 'CV ketel (solo) tot 80KW',
                'price' => 96
              ),
              array(
                'title' => 'CV ketel (combi) tot 80KW',
                'price' => 111
              ),
              array(
                'title' => 'Moederhaard',
                'price' => 74.5
              ),
              array(
                'title' => 'Gasboiler',
                'price' => 63.5
              ),
              array(
                'title' => 'Gashaard',
                'price' => 62.5
              ),
              array(
                'title' => 'Badgeiser',
                'price' => 58
              ),
              array(
                'title' => 'Keukengeiser',
                'price' => 49
              ),
              array(
                'title' => 'Douchegeiser',
                'price' => 55
              ),
              array(
                'title' => 'WTW unit',
                'price' => 64
              ),
              array(
                'title' => 'Zonneboiler',
                'price' => 52
              ),
              array(
                'title' => 'MV box',
                'price' => 29
              ),
              array(
                'title' => 'Toeslag eenmalig onderhoud',
                'price' => 12.5
              )
            )
          ),
          array(
            'title' => '020 Service',
            'items' => array(
              array(
                'title' => 'CV ketel (solo) tot 40KW',
                'price' => 97
              ),
              array(
                'title' => 'CV ketel (combi) tot 40KW',
                'price' => 101
              ),
              array(
                'title' => 'CV ketel (solo) tot 80KW',
                'price' => 125.5
              ),
              array(
                'title' => 'CV ketel (combi) tot 80KW',
                'price' => 137
              ),
              array(
                'title' => 'Moederhaard',
                'price' => 95
              ),
              array(
                'title' => 'Gasboiler',
                'price' => 84
              ),
              array(
                'title' => 'Gashaard',
                'price' => 73.5
              ),
              array(
                'title' => 'Badgeiser',
                'price' => 73.5
              ),
              array(
                'title' => 'Keukengeiser',
                'price' => 68
              ),
              array(
                'title' => 'Douchegeiser',
                'price' => 72
              ),
              array(
                'title' => 'WTW unit',
                'price' => 91
              ),
              array(
                'title' => 'Zonneboiler',
                'price' => 78
              ),
              array(
                'title' => 'MV box',
                'price' => 40
              ),
              array(
                'title' => 'Toeslag eenmalig onderhoud',
                'price' => 12.5
              )
            )
          ),
          array(
            'title' => '030 All-in',
            'items' => array(
              array(
                'title' => 'CV ketel (solo) tot 40KW',
                'price' => 139.5
              ),
              array(
                'title' => 'CV ketel (combi) tot 40KW',
                'price' => 158
              ),
              array(
                'title' => 'CV ketel (solo) tot 80KW',
                'price' => 167.5
              ),
              array(
                'title' => 'CV ketel (combi) tot 80KW',
                'price' => 179.5
              ),
              array(
                'title' => 'Toeslag eenmalig onderhoud',
                'price' => 12.5
              )
            )
          ),
          array(
            'title' => 'OGP',
            'items' => array(
              array(
                'title' => 'CV ketel (combi) tot 40KW',
                'price' => 158
              )
            )
          ),
        )
      ),
      array(
        'title' => 'Arbeidsloon',
        'items' => array(
          array(
            'title' =>'Klanten',
            'items' => array(
              array(
                'title' =>'Ma t/m Vrijdag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 64
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 16
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 32
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 48
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 38
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 54
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 70
                  ),
                )
              ),
              array(
                'title' =>'Avond',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 90
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 22.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 45
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 67.5
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 47
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 69.5
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 92
                  ),
                )
              ),
              array(
                'title' =>'Zaterdag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 102
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 25.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 51
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 76.5
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 52
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 77.5
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 103
                  ),
                )
              ),
              array(
                'title' =>'Zondag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 127.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 31.88
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 63.76
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 95.64
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 65
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 96.88
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 128.76
                  ),
                )
              )
            )
          ),
          array(
            'title' =>'Niet klanten',
            'items' => array(
              array(
                'title' =>'Ma t/m Vrijdag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 72
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 18
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 36
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 54
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 42
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 60
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 78
                  ),
                )
              ),
              array(
                'title' =>'Avond',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 94
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 23.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 47
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 70.5
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 56
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 79.5
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 103
                  ),
                )
              ),
              array(
                'title' =>'Zaterdag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 107
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 26.75
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 53.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 80.25
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 60
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 86.75
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 113.5
                  ),
                )
              ),
              array(
                'title' =>'Zondag',
                'items' => array(
                  array(
                    'title' => 'Arbeidsloon per uur',
                    'price' => 145
                  ),
                  array(
                    'title' => 'Arbeidsloon per 15 min.',
                    'price' => 36.25
                  ),
                  array(
                    'title' => 'Arbeidsloon per 30 min.',
                    'price' => 72.5
                  ),
                  array(
                    'title' => 'Arbeidsloon per 45 min.',
                    'price' => 108.75
                  ),
                  array(
                    'title' => 'Voorrijtarief',
                    'price' => 70
                  ),
                  array(
                    'title' => 'Basistarief 1-14 min.',
                    'price' => 106.25
                  ),
                  array(
                    'title' => 'Basistarief 15-30 min.',
                    'price' => 142.5
                  ),
                )
              )
            )
          )
        )
      )

    );

    /*
        $products = array(
          array(
            'title' => 'A',
            'items' => array(
              array(
                'title' => 'B',
              ),
              array(
                'title' => 'C',
                'items' => array(
                  array(
                    'title' => 'E',
                    'items' => array(
                      array(
                        'title' => 'F',
                      ),
                      array(
                        'title' => 'G',
                      )
                    ))
                )
              ),
              array(
                'title' => 'D'
              )
            )
          )
        );
    */
    /*
        echo '<pre>';
        $root = new Category;
        $root->setTreeLeft(1);
        $root->setTree(1);
        $root->setCompanyId(2);
        $root->setTitle('root');
        $root->save();


        $this->recurseTree($products, $root->getTreeLeft(), 0, $root->getId());
        var_dump($products);
        exit;
    */
    $c = new Criteria;
    $c->add(CategoryPeer::TREE_PARENT, null, Criteria::ISNULL);
    $c->add(CategoryPeer::COMPANY_ID, 2);
    $root = CategoryPeer::doSelectOne($c);
    $nodes = $this->recurseNode($root);

    $this->nodes = $nodes;

    $this->getUser()->setAttribute('subnav', array(
      array(
        'title' => 'Instellingen',
        'items' => array(
          'Settings.general();' => array('general' => 'Algemeen'),
          'Settings.invoices();' => array('invoices' => 'Factuurgegevens'),
          'Settings.products();' => array('products' => 'Producten'),
        )
      ),
      array(
        'title' => 'Gebruikers',
        'items' => array(
          'Settings.resources();' => array('resources' => 'Medewerkers'),
          'Settings.login();' => array('login' => 'Beheerder')
        )
      ),
    ));
    $this->getUser()->setAttribute('buttons', array(
      //array('label' => 'Opslaan', 'action' => "alert('Hier kom de opslaan functie achter');"),
    ));

    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);
    $this->credentials = $credentials;

    $c = new Criteria;
    $c->add(AdministratorPeer::CREDENTIALS_ID, $credentials->getId());
    $administrator = AdministratorPeer::doSelectOne($c);
    $this->forward404Unless($administrator);
    $this->administrator = $administrator;

    $company = CompanyPeer::retrieveByPK($credentials->getCompanyId());
    $this->company = $company;
  }

  public function executeSettingsData()
  {
    $result = array();

    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);
    $company = CompanyPeer::retrieveByPK($credentials->getCompanyId());
    $this->forward404Unless($company);

    $c = new Criteria;
    $c->add(AdministratorPeer::CREDENTIALS_ID, $credentials->getId());
    $administrator = AdministratorPeer::doSelectOne($c);
    $this->forward404Unless($administrator);
    $this->administrator = $administrator;

    $method = $this->hasRequestParameter('method') ? $this->getRequestParameter('method') : 'load';

    $errors = array();
    switch ($this->getRequestParameter('form')) {
      case 'invoices':
      case  'invoices-preview':
        if (!$this->validate('companyname',  'required')) {
          $errors['companyname'] = 'Bedrijfsnaam is een verplicht veld.';
        }
        if (!$this->validate('kvk',  'required')) {
          $errors['kvk'] = 'KvK nummer is een verplicht veld.';
        }
        if (!$this->validate('iban',  'required')) {
          $errors['iban'] = 'IBAN nummer is een verplicht veld.';
        }
        if (!$this->validate('iban_name',  'required')) {
          $errors['iban_name'] = 'IBAN rekeninghouder is een verplicht veld.';
        }
        if (!$this->validate('btw',  'required')) {
          $errors['btw'] = 'BTW-nummer is een verplicht veld.';
        }
        if (!$this->validate('color1',  'required')) {
          $errors['color1'] = 'Kleur 1 is een verplicht veld.';
        }
        else if (!$this->validate('color1',  'color')) {
          $errors['color1'] = 'Kleur 1 is een verplicht veld.';
        }
        if (!$this->validate('color2',  'required')) {
          $errors['color2'] = 'Kleur 2 is een verplicht veld.';
        }
        else if (!$this->validate('color2',  'color')) {
          $errors['color2'] = 'Kleur 2 is een verplicht veld.';
        }

        if (count($errors) == 0) {
          if ($this->getRequestParameter('form') == 'invoices') {
            $company->setSetting('companyname', $this->getRequestParameter('companyname'));
            $company->setSetting('kvk', $this->getRequestParameter('kvk'));
            $company->setSetting('iban', $this->getRequestParameter('iban'));
            $company->setSetting('iban_name', $this->getRequestParameter('iban_name'));
            $company->setSetting('btw', $this->getRequestParameter('btw'));
            $company->setSetting('color1', $this->getRequestParameter('color1'));
            $company->setSetting('color2', $this->getRequestParameter('color2'));
            $company->setSetting('email', $this->getRequestParameter('email'));
            $company->setSetting('site', $this->getRequestParameter('site'));
            $company->setSetting('logo', basename($this->getRequestParameter('logo-fld')));
          }
          else if ($this->getRequestParameter('form') == 'invoices-preview') {
            $params['documenttype'] = 'Factuur';
            $params['title'] = 'WO-12345';
            $params['invoicenr'] = 'DEMO-'.time();
            $params['customernr'] = 'D0123';
            $params['enddate'] = date('Y-m-d', strtotime('+4 weeks'));
            $params['customer'] = 'V. Oorbeeld'.PHP_EOL.'Stationstraat 123'.PHP_EOL.'1234AA Ergenshuizen';
            $params['remarks'] = 'Opmerkingen bij deze factuur';
            $params['ready'] = true;
            $params['payment'] = array(
              'paymethod' => 'invoice'
            );

            $rows = array(
              array('type' => 'product', 'amount' => 3, 'cost' => 15, 'desc' => 'Voorbeeld product'),
              array('type' => 'hours', 'minutes' => 60, 'cost' => 50, 'desc' => 'Voorbeeld arbeidstijd')
            );
            foreach ($rows as $row) {
              $tariff = $amount = 0;
              switch ($row['type']) {
                case 'hours':
                  $tariff = 50;
                  if ($row['minutes'] > 0) {
                    $amount = round(60 / $row['minutes'], 1);
                  }
                  break;
                case 'product':
                  $tariff = $row['cost'];
                  $amount = $row['amount'];
                  break;
                case 'activity':
                  $tariff = $row['cost'];
                  $amount = 1;
                  break;
              }
              $params['rows'][] = array(
                'type' => $row['desc'],
                'tariff' => $tariff,
                'amount' => $amount
              );
            }

            $params['companyname'] = $this->getRequestParameter('companyname');
            $params['kvk'] = $this->getRequestParameter('kvk');
            $params['btw'] = $this->getRequestParameter('btw');
            $params['iban'] = $this->getRequestParameter('iban');
            $params['iban_name'] = $this->getRequestParameter('iban_name');
            $params['site'] = $this->getRequestParameter('site');
            $params['email'] = $this->getRequestParameter('email');
            $params['invoicedays'] = $company->getSetting('invoicedays');
            $params['color1'] = $this->getRequestParameter('color1');
            $params['color2'] = $this->getRequestParameter('color2');
            $params['logo'] = basename($this->getRequestParameter('logo-fld'));
            $params['sender_name'] = $company->getSetting('sender_name');
            $params['sender_email'] = $company->getSetting('sender_email');
            $params['admin_email'] = $company->getSetting('admin_email');

            $invoice = $this->generateInvoice($params);

            $result['download-link'] = 'http://'.$_SERVER['SERVER_NAME'].'/invoices/'.basename($invoice);
          }
        }
        break;
      case 'general':

        if (!$this->validate('companyname2',  'required')) {
          $errors['companyname2'] = 'Bedrijfsnaam is een verplicht veld.';
        }
        if (!$this->validate('address',  'required')) {
          $errors['address'] = 'Adres is een verplicht veld.';
        }
        if (!$this->validate('zipcode',  'required')) {
          $errors['zipcode'] = 'Postcode is een verplicht veld.';
        }
        else if (!$this->validate('zipcode',  'zipcode')) {
          $errors['zipcode'] = 'Postcode is niet juist ingevoerd. Geldige notatie is 1234AA.';
        }
        if (!$this->validate('city',  'required')) {
          $errors['city'] = 'Bedrijfsnaam is een verplicht veld.';
        }

        if (!$this->validate('sender_name',  'required')) {
          $errors['sender_name'] = 'Afzendernaam is een verplicht veld.';
        }
        if (!$this->validate('sender_email',  'required')) {
          $errors['sender_email'] = 'Afzender e-mail adres is een verplicht veld.';
        }
        else if (!$this->validate('sender_email',  'email')) {
          $errors['sender_email'] = 'Afzender e-mail adres is geen geldig e-mail adres.';
        }
        if (!$this->validate('admin_email',  'required')) {
          $errors['admin_email'] = 'Admin e-mail adres is een verplicht veld.';
        }
        else if (!$this->validate('admin_email',  'email')) {
          $errors['admin_email'] = 'Admin e-mail adres is geen geldig e-mail adres.';
        }
        if (!$this->validate('invoicedays',  'required')) {
          $errors['invoicedays'] = 'Betaaltermijn facturen is een verplicht veld.';
        }
        else if (!$this->validate('invoicedays',  'numeric')) {
          $errors['invoicedays'] = 'Betaaltermijn facturen moet een getal zijn.';
        }

        if (count($errors) == 0) {
          $company->setSetting('companyname', $this->getRequestParameter('companyname2'));
          $company->setPhone($this->getRequestParameter('phone1'));
          $company->save();
          $address = $company->getAddress();
          $address->setAddress($this->getRequestParameter('address'));
          $address->setZipcode($this->getRequestParameter('zipcode'));
          $address->setCity($this->getRequestParameter('city'));
          $address->save();


          $company->setSetting('sender_name', $this->getRequestParameter('sender_name'));
          $company->setSetting('sender_email', $this->getRequestParameter('sender_email'));
          $company->setSetting('admin_email', $this->getRequestParameter('admin_email'));
          $company->setSetting('invoicedays', $this->getRequestParameter('invoicedays'));

          $connection = $company->getConnection();
          $connection->setApiKey($this->getRequestParameter('api_key'));
          $connection->setApiSecret($this->getRequestParameter('api_secret'));
          $connection->setApiServer($this->getRequestParameter('api_server'));
          $connection->save();
        }

        break;
      case 'login':
        if (!$this->validate('admin-title',  'required')) {
          $errors['admin-title'] = 'Naam is een verplicht veld.';
        }
        if (!$this->validate('admin-email',  'required')) {
          $errors['admin-email'] = 'E-mail adres is een verplicht veld.';
        }
        else if (!$this->validate('admin-email',  'email')) {
          $errors['admin-email'] = 'E-mail adres is geen geldig e-mail adres.';
        }
        if (!$this->validate('admin-username',  'required')) {
          $errors['admin-username'] = 'Gebruikersnaam is een verplicht veld.';
        }
        else if(!$this->validate('admin-username', 'unique', array('current' => $credentials->getUsername()))) {
          $errors['admin-username'] = 'Gebruikersnaam is al in gebruik.';
        }
        $passwordChange = false;
        if ($this->validate('admin-password1',  'required') && $this->validate('admin-password2',  'required')) {

          if (!$this->validate('admin-password1', 'password')) {
            $errors['admin-password1'] = "Wachtwoord is niet sterk genoeg. Probeer een langer wachtwoord met hoofdletters en kleine letters.";
          }
          else if (!$this->validate('admin-password1',  'compare', array('check' => $this->getRequestParameter('admin-password2')))) {
            $errors['admin-password2'] = 'Controle wachtwoord is niet gelijk aan wachtwoord.';
          }
          else {
            $passwordChange = true;
          }
        }


        if(count($errors) == 0) {
          $administrator->setTitle($this->getRequestParameter('admin-title'));
          $administrator->setEmail($this->getRequestParameter('admin-email'));
          $administrator->save();

          $credentials->setUsername($this->getRequestParameter('admin-username'));
          if ($passwordChange) {
            $salt = md5(time());
            $hash = hash('sha512', $this->getRequestParameter('admin-password1').$salt);
            $credentials->setPassword($hash);
            $credentials->setSalt($salt);
          }
          $credentials->save();
        }
        break;
      case 'products':
        $category = CategoryPeer::retrieveByPK($this->getRequestParameter('id'));
        if (!$category) {
          $category = new Category;
          $category->setCompanyId($credentials->getCompanyId());
          $root = $this->getRoot();
          //$category->insertAsLastChildOf($root);

          $product = new Product;
          $product->setCompanyId($credentials->getCompanyId());
        }
        else {
          $c = new Criteria;
          $c->add(ProductCategoryPeer::CATEGORY_ID, $category->getId());
          $pc = ProductCategoryPeer::doSelectOne($c);
          if (!$pc) {
            $product = new Product;
            $product->setCompanyId($credentials->getCompanyId());
            $product->save();

            $pc = new ProductCategory;
            $pc->setProductId($product->getId());
            $pc->setCategoryId($category->getId());
            $pc->save();
          }
          else {
            $product = $pc->getProduct();
          }

        }
        switch($method) {
          case 'load':
            $result['description'] = $product->getTitle() ? $product->getTitle() : $category->getTitle();
            $result['price'] = $product->getPrice();
            $result['type'] = $category->getChildren() ? 'category' : 'product';
            break;
          case 'save':
            $errors = array();

            if (!$this->validate('description', 'required')) {
              $errors['product-description'] = 'Omschrijving is een verplicht veld.';
            }

            if (count($errors) == 0) {
              $product->setTitle($this->getRequestParameter('description'));
              $product->setPrice($this->getRequestParameter('price'));
              $product->setVat(21);
              $product->setInclvat(true);
              $product->save();

              $category->setTitle($this->getRequestParameter('description'));
              $category->save();

              if (!$pc) {
                $pc = new ProductCategory;
                $pc->setProductId($product->getId());
                $pc->setCategoryId($category->getId());
                $pc->save();
              }

              $c = new Criteria;
              $c->add(CategoryPeer::TREE_PARENT, null, Criteria::ISNULL);
              $c->add(CategoryPeer::COMPANY_ID, $credentials->getCompanyId());
              $root = CategoryPeer::doSelectOne($c);
              $result['products'] = $this->recurseNode($root);

              $result['status'] = 'success';
            }
            else {
              $result['status'] = 'failure';
              $result['errors']= $errors;
            }

            break;

          case 'delete':
            $data['id'] = $category->getId();
            $c = new Criteria;
            $c->add(ProductCategoryPeer::CATEGORY_ID, $category->getId());
            $pcs = ProductCategoryPeer::doSelect($c);
            foreach ($pcs as $pc) {
              $product = $pc->getProduct();
              if ($product) {
                $product->delete();
              }
              $pc->delete();
            }
            $category->delete();
            if ($category->isDeleted()) {
              $c = new Criteria;
              $c->add(CategoryPeer::TREE_PARENT, null, Criteria::ISNULL);
              $c->add(CategoryPeer::COMPANY_ID, $credentials->getCompanyId());
              $root = CategoryPeer::doSelectOne($c);
              $result['products'] = $this->recurseNode($root);
              $result['status'] = 'success';
            }
            else {
              $result['status'] = 'failure';
            }
            break;
          case 'sortorder':
            $data = $this->getRequestParameter('sortable-tree');
            $root = $this->getRoot();
            $left = $this->recurseSaveorder($data, $root, 0);
            $root->setTreeRight($left);
            $root->save();
            $result['products'] = $this->recurseNode($root);
            $result['message'] = 'De nieuwe volgorde is opgeslagen.';
            break;
        }
        break;
      default:
        break;
    }

    if (count($errors) > 0) {
      $result['status'] = 'error';
      $result['errors'] = $errors;
    }
    else {
      $result['status'] = 'success';
      $result['message'] = 'Je wijzigingen zijn opgeslagen.';
    }

    ob_start();
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';
    $result['data'] = ob_get_clean();

    header('Content-type: application/json');
    echo json_encode($result);
    exit;
  }

  private function recurseSaveorder($node, $parent, $left, $sort_fix = array())
  {

    static $done = array();
    foreach ($node as $child)
    {
      if (in_array($child['id'], $done)) continue;
      $done[] = $child['id'];
      $object = CategoryPeer::retrieveByPk($child['id']);
      $left++;
      $object->setTreeLeft($left);

      //$object->insertAsLastChildOf($parent);


      $children = array();
      foreach ($child as $k => $v) {
        if (is_numeric($k)) {
          $children[] = $v;
        }
      }

      if(count($children) > 0) {
        $left = $this->recurseSaveorder($children, $object, $left);
      }
      $left++;
      $object->setTreeRight($left);

      $object->setTreeParent($parent->getId());
      //echo $object->getId().' '.$object->getTreeLeft().' '.$object->getTreeRight().' '.$object->getTreeParent()."\n";
      if($object->save()) {
        //echo 'error';
        //var_dump($object->getTreeParent());
        //exit;
      }
    }

    return $left;

  }

  private function getRoot()
  {
    $c = new Criteria;
    $c->add(CategoryPeer::TREE_PARENT, null, Criteria::ISNULL);
    $root = CategoryPeer::doSelectOne($c);
    return $root;
  }

  public function validate($field, $validator, $cfg = array()) {
    switch($validator) {
      case 'required':
        if($this->hasRequestParameter($field) && strlen($this->getRequestParameter($field)) > 0) {
          return true;
        }
        break;

      case 'color':
        if (strlen($this->getRequestParameter($field)) <> 6) return false;
        return (preg_match('/[a-f0-9]{6}/i', $this->getRequestParameter($field)));
        break;

      case 'numeric':
        return is_numeric($this->getRequestParameter($field));
        break;

      case 'email':
        return (preg_match('/[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]/', $this->getRequestParameter($field)));
        break;

      case 'zipcode':
        return (preg_match('/[0-9]{4}[a-zA-Z]{2}/', $this->getRequestParameter($field)));
        break;

      case 'unique':
        if ($cfg['current'] == $this->getRequestParameter($field)) return true;
        // check uniqueness of username
        $c = new Criteria;
        $c->add(CredentialsPeer::USERNAME, $this->getRequestParameter($field));
        return !(bool)CredentialsPeer::doSelectOne($c);
        break;

      case 'password':
        return strlen($this->getRequestParameter($field)) > 5;
        break;

      case 'compare':
        return $this->getRequestParameter($field) == $cfg['check'];
        break;

    }
    return false;
  }

  public function executeSettingsAjax()
  {

  }

  private function recurseNode($node) {
    $nodes = array();
    foreach ($node->getChildren() as $child) {
      $current = array(
        'title' => $child->getTitle(),
        'id' => $child->getId()
      );
      if($child->hasChildren()) {
        $current['children'] = $this->recurseNode($child);
      }
      $nodes[] = $current;
    }
    return $nodes;
  }

  private function recurseTree($data, $left = 0, $level = 0, $parent = null)
  {
    foreach ($data as $item) {
      $left++;
      if (isset($item['items']) && count($item['items']) > 0) {
        $category = new Category;
        $category->setCompanyId(2);
        $category->setTree(1);
        $category->setTreeLeft($left);

        $category->setTreeParent($parent);
        $category->setTitle($item['title']);
        $category->save();

        $right = $this->recurseTree($item['items'], $left, $level + 1, $category->getId());
        echo $item['title']."\t\t\tL{$left}\t{$right}\tV{$level}\n";

        $category->setTreeRight($right);
        $category->save();

      }
      else {
        $right = $left + 1;
        echo $item['title']." (product) \t\t\tL{$left}\t{$right}\tV{$level}\n";

        $category = new Category;
        $category->setCompanyId(2);
        $category->setTree(1);
        $category->setTreeLeft($left);
        $category->setTreeRight($right);
        $category->setTreeParent($parent);
        $category->setTitle($item['title']);
        $category->save();

        $product = new Product;
        $product->setCompanyId(2);
        $product->setTitle($item['title']);
        $product->setPrice($item['price']);
        $product->setVat(21);
        $product->setInclvat(true);
        $product->save();

        $link = new ProductCategory;
        $link->setCategoryId($category->getId());
        $link->setProductId($product->getId());
        $link->save();
      }
      $left = $right;

    }
    return $right + 1;


    /*
     * $products = array(
        array(
          'title' => 'Diensten',
          'items' => array(
            array(
              'title' => '010 Onderhoud',
              'items' => array(
                array(
                  'title' => 'CV ketel (solo) tot 40KW',
                  'price' => 74.5
     */
  }

  public function executeUpload(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('new_file', $_FILES);

    $destination = sfConfig::get('sf_upload_dir').'/'.$_FILES['Filedata']['name'];

    if (file_exists($destination)) {
      // rename file
      $c = 1;
      $unique = false;
      $parts = explode('.', $destination);
      $ext = array_pop($parts);
      $base = implode('.', $parts);

      while (!$unique && $c < 20) {
        $try = $base.'-'.$c.'.'.$ext;

        //echo $try."\n";
        if (!file_exists($try)) {
          $destination = $try;
          $unique = true;
        }
        $c++;
      }
    }

    move_uploaded_file($_FILES['Filedata']['tmp_name'], $destination);
    sfConfig::set('sf_web_debug', false);
    $target = '/img/logo/'.basename($destination);
    copy($destination, sfConfig::get('sf_web_dir').$target);
    $thumb = zeusImages::getPresentation($target, array( 'width' => 240, 'height' => 160, 'resize_method' => zeusImages::RESIZE_CHOP));
    echo $thumb;
    return sfView::NONE;

  }

  public function executeUpdate(sfWebRequest $request, $customConfig = false)
  {
    $this->setTemplate(sfConfig::get('sf_plugins_dir').'/zeusCorePlugin/modules/filesadmin/templates/update');
  }

  public function executeResourcesData()
  {
    header('Content-type: application/json');

    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $id = $this->getRequestParameter('id');

    $data = array('status' => 'failure');

    $method = $this->hasRequestParameter('method') ? $this->getRequestParameter('method') : 'load';
    $resource = ResourcePeer::retrieveByPk($id);
    if (!$resource) {
      $resource = new Resource;
      $resource->setCompanyId($credentials->getCompanyId());
      $resource_credentials = new Credentials;
      $resource_credentials->setType('resource');
      $resource_credentials->setCompanyId($credentials->getCompanyId());
    }
    else {
      $resource_credentials = $resource->getCredentials();
    }
    if ($resource) {
      switch ($method) {
        case 'load':
          $c = new Criteria;
          $c->add(WorkorderPeer::RESOURCE_ID, $id);
          $workorders = WorkorderPeer::doSelect($c);

          $resource_credentials = $resource->getCredentials();
          $username = $resource_credentials ? $resource_credentials->getUsername() : '';

          $data = array(
            'id' => $id,
            'title' => $resource->getName(),
            'email' => $resource->getEmail(),
            'phone' => $resource->getPhone(),
            'username' => $username,
            'resource-oa' => $resource->getOaResourceId(),
            'active' => $resource_credentials ? $resource_credentials->getActive() : false,
            'workorders' => array()
          );

          foreach ($workorders as $workorder) {
            $data['workorders'][] = array(
              'status' => $workorder->getStatusStr(),
              'customer' => $workorder->getCustomer()->getFullName(),
              'address' => $workorder->getAddress()->getFullAddress(),
              'date' => date('d-m-Y', strtotime($workorder->getDate())),
              'ready' => (bool)$workorder->getReady(),
              'id' => $workorder->getId()
            );
          }

          $data['status'] = 'success';
          break;

        case 'save':
          $errors = array();

          if (!$this->validate('title', 'required')) {
            $errors['resource-title'] = 'Naam is een verplicht veld.';
          }
          if (!$this->validate('email', 'required')) {
            $errors['resource-email'] = 'E-mail adres is een verplicht veld.';
          }
          else if (!$this->validate('email', 'email')) {
            $errors['resource-email'] = 'E-mail adres is geen geldig e-mail adres.';
          }

          if (!$this->validate('username',  'required')) {
            $errors['resource-username'] = 'Gebruikersnaam is een verplicht veld.';
          }
          else if(!$this->validate('username', 'unique', array('current' => $resource_credentials->getUsername()))) {
            $errors['resource-username'] = 'Gebruikersnaam is al in gebruik.';
          }
          $passwordChange = false;
          if ($this->validate('password1',  'required') && $this->validate('password2',  'required')) {

            if (!$this->validate('password1', 'password')) {
              $errors['resource-password1'] = "Wachtwoord is niet sterk genoeg. Probeer een langer wachtwoord met hoofdletters en kleine letters.";
            }
            else if (!$this->validate('password1',  'compare', array('check' => $this->getRequestParameter('password2')))) {
              $errors['resource-password2'] = 'Controle wachtwoord is niet gelijk aan wachtwoord.';
            }
            else {
              $passwordChange = true;
            }
          }

          if (count($errors) == 0) {

            $resource->setName($this->getRequestParameter('title'));
            $resource->setEmail($this->getRequestParameter('email'));
            $resource->setPhone($this->getRequestParameter('phone'));
            $resource->setOaResourceId($this->getRequestParameter('resource-oa'));
            $resource->setTeamId(1);

            $resource_credentials->setUsername($this->getRequestParameter('username'));
            if ($passwordChange) {
              $salt = md5(time());
              $hash = hash('sha512', $this->getRequestParameter('password1').$salt);
              $resource_credentials->setPassword($hash);
              $resource_credentials->setSalt($salt);
            }
            $resource_credentials->setActive($this->getRequestParameter('active') == 'on');
            $resource_credentials->save();

            $resource->setCredentialsId($resource_credentials->getId());
            $resource->save();

            $data['status'] = 'success';
          }
          else {
            $data['status'] = 'failure';
            $data['errors']= $errors;
          }





          break;

        case 'delete':
          $data['id'] = $resource->getId();
          $resource->delete();
          if ($resource->isDeleted()) {
            $data['status'] = 'success';
          }
          else {
            $data['status'] = 'failure';
          }

      }
    }

    echo json_encode($data);
    exit;
  }

  public function executeResourcesAjax()
  {
    $credentials_id = $this->getUser()->getAttribute('userid');
    $credentials = CredentialsPeer::retrieveByPk($credentials_id);
    $this->forward404Unless($credentials);

    $c = new Criteria;
    $c->add(ResourcePeer::COMPANY_ID, $credentials->getCompanyId());
    $total = ResourcePeer::doCount($c);

    $offset = $this->hasRequestParameter('offset') ? (int)$this->getRequestParameter('offset') : 0;
    $c->setOffset($offset);
    $c->setLimit(250);
    $resources = ResourcePeer::doSelect($c);
    $data = array();
    foreach ($resources as $resource) {
      $data[] = array(
        $resource->getId(),
        $resource->getName(),
        strlen($resource->getEmail()) > 20 ? substr($resource->getEmail(),0,20).'..' : $resource->getEmail(),
        $resource->getPhone()
      );
    }
    $this->data = $data;
    header('Content-type: application/json');

    echo json_encode(array('data' => $data, 'offset' => $offset, 'limit' => 100, 'total' => $total));
    exit;
  }

  public function executeLogin(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('crumblepath', array('inloggen'));

    $this->setLayout('layout-home');

    $this->form = new werkbonLoginForm;

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('werkbonlogin'));
      if ($this->form->isValid())
      {
        $this->getUser()->setAuthenticated(true);
        $this->redirect('admin/planboard');
      }
    }
  }

  public function executeLogoff(sfWebRequest $request)
  {
    $this->getUser()->setAuthenticated(false);
    $this->redirect('@homepage');
  }

  public function executeTextEditor()
  {
    $partial = PartialPeer::findByName($this->getRequestParameter('editorId'));
    if (!$partial) {
      $partial = new Partial;
      $partial->setKey($this->getRequestParameter('editorId'));
    }
    $partial->setText($this->getRequestParameter('value'));
    $partial->save();
    echo $this->getRequestParameter('value');
    return sfView::NONE;
  }

  public function generateInvoice($params = array())
  {
    $color1_r = hexdec(substr($params['color1'],0,2));
    $color1_g = hexdec(substr($params['color1'],2,2));
    $color1_b = hexdec(substr($params['color1'],4,2));

    $color2_r = hexdec(substr($params['color2'],0,2));
    $color2_g = hexdec(substr($params['color2'],2,2));
    $color2_b = hexdec(substr($params['color2'],4,2));

    $pdf= new PDF();
    $pdf->AddPage();
    $pdf->AddFont('Futura');
    $pdf->AddFont('Futura', 'B');
    $pdf->SetFont('Futura','',14);
    $pdf->SetRightMargin(0);
    $pdf->SetFillColor(247,247,247);
    $pdf->Rect(0,0,220,28, 'F');
    $pdf->Image(getcwd().'/img/logo/'.$params['logo'],10,2, 45);

    $pdf->setY(5);
    $pdf->setX(100);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->Write(4, $params['companyname']);
    $pdf->Ln(4);
    $pdf->SetFontSize(8);
    $pdf->SetTextColor($color1_r,$color1_g,$color1_b);
    $pdf->setX(100);
    $pdf->Write(5, $params['site'].' | '.$params['email']);
    $pdf->Ln(4);
    $pdf->setX(100);
    $pdf->Write(5, 'KvK '.$params['kvk'].' | BTW '.$params['btw'].' | IBAN '.$params['iban']);
    $pdf->Ln(8);
    $pdf->setX(100);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->SetFontSize(14);
    $pdf->SetStyle('B',true);
    $pdf->Write(5, strtoupper($params['documenttype']));
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);

    $pdf->SetStyle('B',true);
    $pdf->SetFontSize(16);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->Ln(5);
    $pdf->Write(5,$params['title']);

    $nr = $params['title'];

    $offset = 0;

    $pdf->Ln(10);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor($color1_r,$color1_g,$color1_b);
    $pdf->Write(5, 'Uw gegevens');
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);
    $parts = explode("\n",$params['customer']);
    if (count($parts) < 5) {
      for($c = count($parts); $c < 5; $c++) {
        $parts[] = '';
      }
    }
    $parts = array_slice($parts,0,5);
    foreach ($parts as $part) {
      $pdf->Write(5, html_entity_decode($part));
      $pdf->Ln(5);
    }
    $offset += ((count($parts) - 3) * 5);

    $pdf->Ln(5);


    $pdf->SetStyle('B',true);
    $pdf->Write(5, 'Kenmerken');
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);

    $pdf->Write(5, 'Factuurdatum');
    $pdf->Write(5, '');
    $pdf->SetX(50);
    $pdf->Write(5, date('d-m-Y', strtotime($params['enddate'])));
    $pdf->Write(5, '');
    $pdf->Ln(5);
    $pdf->Write(5, 'Factuurnummer');
    //$pdf->Write(5, 'Ordernummer');
    $pdf->SetX(50);
    $code =  $params['invoicenr'];

    $pdf->Write(5, $code);
    //$pdf->Write(5, $invoice->getCode());
    $pdf->Ln(5);
    $pdf->Write(5, 'Debiteurnummer');
    $pdf->SetX(50);
    $pdf->Write(5, $params['customernr']);

    $pdf->Ln(10);

    $pdf->SetStyle('B',true);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->Write(5, 'Omschrijving');
    $pdf->SetX(120);
    $pdf->Write(5, 'Uren/Aantal');
    $pdf->SetX(150);
    $pdf->Write(5, 'Tarief');
    $pdf->SetX(180);
    $pdf->Write(5, 'Totaal');
    $pdf->Ln(10);

    $pdf->SetLineWidth(0.3);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,98+$offset,200,98+$offset);

    $pdf->SetTextColor($color1_r,$color1_g,$color1_b);
    $pdf->SetStyle('B',false);

    $rows = array();
    $total = 0;
    foreach ($params['rows'] as $row) {
      $rows[] = array(
        $row['type'],
        $row['amount'],
        '€ '.str_replace(',00', ',-', number_format($row['tariff'] , 2, ',', '.')),
        '€ '.str_replace(',00', ',-', number_format($row['amount']*$row['tariff'] , 2, ',', '.'))
      );
      $total += ($row['amount']*$row['tariff']);
    }

    for ($c = count($rows); $c < (24 - ($offset/5)); $c++)
    {
      $rows[] = array('', '', '', '');
    }

    foreach ($rows as $row) {
      $pdf->Write(5, $row[0]);
      $pdf->SetX(120);
      $pdf->SetFont('Futura','',9);
      $pdf->Write(5, $row[1]);

      $pdf->SetFont('Arial','',9);
      $pdf->SetX(150);
      $pdf->Write(5, $row[2]);
      $pdf->SetX(180);
      $pdf->SetFont('Arial','',9);
      $pdf->Write(5, $row[3]);
      $pdf->SetFont('Futura','',9);
      $pdf->Ln(5);
    }

    $pdf->SetLineWidth(0.3);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,223,200,223);
    $pdf->Ln(5);

    $vat = strtotime($params['enddate']) < strtotime(date('2012-10-01')) && strtotime($params['enddate']) > 0 ? 19 : 21;
    //$vat_factor = (100 + $vat) / 100;
    $vat_factor = 100 + $vat;

    $total_ex = ($total / $vat_factor)*100;

//  $trtotime($invoice->getDate()) < strtotime(date('2012-10-01')) ? 19 : 21;
    //$vat_factor = (100 + $vat) / 100;

    //$total_ex = $invoice->getHourrate() * $time;
    //$total = ($invoice->getHourrate() * $time) * $vat_factor;

    $pdf->SetX(140);
    $pdf->Write(5, 'Totaal exclusief BTW');
    $pdf->SetX(180);
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format($total_ex, 2, ',', '.')));
    $pdf->SetFont('Futura','',10);
    $pdf->Ln(5);

    $pdf->SetX(140);
    $pdf->Write(5, $vat.'% BTW');
    $pdf->SetX(180);
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format($total - $total_ex , 2, ',', '.')));
    $pdf->SetFont('Futura','',10);
    $pdf->Ln(5);

    $pdf->SetLineWidth(0.5);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,238,200,238);
    $pdf->Ln(5);

    $pdf->SetStyle('B',true);
    $pdf->SetFontSize(12);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->SetTextColor($color2_r,$color2_g,$color2_b);
    $pdf->SetX(140);
    $pdf->Write(5, 'Totaal');
    $pdf->SetX(180);
    $pdf->SetFont('Arial','',12);
    $pdf->SetStyle('B',true);
    $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format($total, 2, ',', '.')));
    $pdf->SetFont('Futura','',10);
    $pdf->Ln(5);

    $pdf->SetFont('Futura','',10);
    $pdf->SetStyle('B',false);
    $pdf->SetStyle('B',false);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor($color1_r,$color1_g,$color1_b);
    //$pdf->Ln(($invoice->getPayed() > 0) ? 8 : 18);
    $pdf->Ln(18);

    switch($params['payment']['paymethod']) {
      case 'invoice':

        $pdf->SetX(17);
        $pdf->Write(5, 'Wij verzoeken u vriendelijk het factuurbedrag binnen '.$params['invoicedays'].' dagen na factuurdatum over te maken op bankrekening');
        $pdf->Ln(5);
        $pdf->SetX(33);
        $pdf->Write(2, $params['iban'].' tnv '.$params['iban_name'].' o.v.v. uw debiteurnummer en factuurnummer.');
        break;

      case 'cash':
        $pdf->SetX(17);
        $pdf->Write(5, 'Deze factuur is reeds per contant voldaan.');
        $pdf->Ln(5);
        break;

      case 'pin':
        $pdf->SetX(17);
        $pdf->Write(5, 'Deze factuur is reeds per pin-betaling voldaan.');
        $pdf->Ln(5);
        break;
    }


    if (!is_dir(getcwd().'/invoices')) {
      mkdir(getcwd().'/invoices', 0777);
    }
    $pdf->Output(getcwd().'/invoices/'.$params['invoicenr'].'.pdf');

    return getcwd().'/invoices/'.$params['invoicenr'].'.pdf';
    /*
    $server = str_replace('cms.', '', $_SERVER['HTTP_HOST']);

    header('Location: http://'.$server.'/invoices/'.$params['invoicenr'].'.pdf');
    exit;*/
  }

}