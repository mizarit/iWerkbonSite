<h2>Werkbon details</h2>
<table>
  <tr>
    <?php
   // $workorder = new Workorder();
    ?>
    <td style="width: 220px;font-weight: bold;">Status</td>
    <td id="workorder-view-status"><?php echo $workorder->getStatusStr(); ?></td>
  </tr>
  <tr>
    <td>Datum</td>
    <td id="workorder-view-date"><?php echo date('d-m-Y', strtotime($workorder->getDate())); ?></td>
  </tr>
  <tr>
    <td>Opmerkingen</td>
    <td id="workorder-view-remarks"><?php echo $workorder->getRemarks(); ?></td>
  </tr>
  <tr>
    <td>Gereed</td>
    <td id="workorder-view-ready"><?php echo $workorder->getReady() ?  'Ja' : 'Nee'; ?></td>
  </tr>
  <tr>
    <td>Medewerker</td>
    <td id="workorder-view-resource"><?php echo $workorder->getResource()->getName(); ?></td>
  </tr>
  <tr>
    <td>Handtekening klant</td>
    <td id="workorder-view-signature"><img src="<?php echo $workorder->getSignature(); ?>"></td>
  </tr>
  <?php
  $c = new Criteria;
  $c2 = new Criteria;
  $c->add(FieldPeer::COMPANY_ID, $company->getId());
  $c->add(FieldPeer::ACTIVE, true);
  $c->add(FieldPeer::FORM, 'app');
  $fields = FieldPeer::doSelect($c);
  foreach ($fields as $field) { ?>
  <tr>
    <td><?php echo $field->getLabel(); ?></td>
    <td>
<?php
  $value = '';
    // try to load a value for this field
  $c->clear();
  $c2->clear();
  $c2->add(AppointmentPeer::WORKORDER_ID, $workorder->getId());
  $appointment = AppointmentPeer::doSelectOne($c);
  if ($appointment) {
    $c->add(FieldValuePeer::COMPANY_ID, $company->getId());
    $c->add(FieldValuePeer::FIELD_ID, $field->getId());
    $c->add(FieldValuePeer::OBJECT_ID, $appointment->getId());
    $field_value = FieldValuePeer::doSelectOne($c);
    if ($field_value) $value = $field_value->getValue();
  }
  echo $value;
?>
    </td>
  </tr>
  <?php } ?>
</table>

<h2>Klantgegevens</h2>
<table>
  <tr>
    <td style="width: 220px;font-weight: bold;">Naam</td>
    <td id="customer-view-title"><?php echo $workorder->getCustomer()->getTitle(); ?></td>
  </tr>
  <tr>
    <td>Adres</td>
    <td id="customer-view-address"><?php echo $workorder->getAddress()->getAddress(); ?></td>
  </tr>
  <tr>
    <td>Postcode & plaats</td>
    <td><span id="customer-view-zipcode"><?php echo $workorder->getAddress()->getZipcode(); ?></span> <span id="customer-view-city"><?php echo $workorder->getAddress()->getCity(); ?></span></td>
  </tr>
  <tr>
    <td>E-mail adres</td>
    <td id="customer-view-email"><?php echo $workorder->getCustomer()->getEmail(); ?></td>
  </tr>
  <tr>
    <td>Telefoonnummer</td>
    <td id="customer-view-phone"><?php echo $workorder->getCustomer()->getPhone(); ?></td>
  </tr>
</table>

<h2>Orderregels</h2>
<div id="workorder-orderrows">
  <?php
  $orderrows = json_decode($workorder->getOrderrows(), true);
  if (count($orderrows) > 0) { ?>
    <table>
      <thead>
      <tr>
        <th style="width:220px;">Aantal</th>
        <th>Omschrijving</th>
      </tr>
      </thead>
      <?php foreach ($orderrows as $orderrow) { ?>
        <tr>
          <td><?php echo $orderrow['c']; ?></td>
          <td><?php echo $orderrow['d']; ?></td>
        </tr>
      <?php } ?>
    </table>
    <?php

  }
  else {
    echo 'Deze werkbon heeft geen orderregels.';
  }
  ?>
</div>

<h2>Controlelijsten</h2>
<div id="workorder-checklist">
<?php
// get checklist info
$checklist_d = array();
$c->clear();
$c->add(AppointmentPeer::WORKORDER_ID, $workorder->getId());
$appointment = AppointmentPeer::doSelectOne($c);
  if ($appointment) {
    $c->clear();
    $c->add(ChecklistAppointmentPeer::APPOINTMENT_ID, $appointment->getId());
    $checklists = ChecklistAppointmentPeer::doSelect($c);
    foreach ($checklists as $checklist) {
      $chk = $checklist->getChecklistId();
      $c->clear();
      $c->add(ChecklistRowPeer::CHECKLIST_ID, $chk);
      $c->add(ChecklistRowPeer::ACTIVE, true);
      $items = ChecklistRowPeer::doSelect($c);
      foreach ($items as $item) {
      $c->clear();
      $c->add(ChecklistValuePeer::CHECKLIST_ROW_ID, $item->getId());
      $c->add(ChecklistValuePeer::WORKORDER_ID, $workorder->getId());
      $checked = ChecklistValuePeer::doSelectOne($c);
      $checklist_d[] = array(
        'checklist' => $checklist->getChecklist()->getTitle(),
        'row' => $item->getLabel(),
        'checked' => $checked ? 'Ja' : 'Nee'
      );
    }
  }
}
if (count($checklist_d) > 0) { ?>
  <table>
    <thead>
    <tr>
      <th style="width:220px;">Controlelijst</th>
      <th>Controlepunt</th>
      <th>Afgevinkt</th>
    </tr>
    </thead>
    <?php foreach ($checklist_d as $checklist) { ?>
      <tr>
        <td><?php echo $checklist['checklist']; ?></td>
        <td><?php echo $checklist['row']; ?></td>
        <td><?php echo $checklist['checked']; ?></td>
      </tr>
    <?php } ?>
  </table>
  <?php

} else {
  echo 'Deze werkbon heeft geen controlelijsten.';
}
?>
</div>

<h2>Factuur</h2>
<div id="workorder-invoices">
  <?php
  $c->clear();
  $c->add(InvoicePeer::WORKORDER_ID, $workorder->getId());
  $invoice = InvoicePeer::doSelectOne($c);
  $invoices = array();
  if ($invoice) {
    $invoices[] = array(
      //'status' => $invoice->getStatus(),
      'date' => date('d-m-Y', strtotime($invoice->getDate())),
      'total' => '€ ' . number_format($invoice->getTotal(), 2, ',', '.'),
      'totalv' => $invoice->getTotal(),
      'rows' => json_decode($invoice->getOrderrows()),
      'id' => $invoice->getId()
    );
  }
  if (count($invoices) > 0) { ?>
  <table>
    <thead>
    <tr>
      <th style="width:220px;">Datum</th>
      <th>Totaal</th>
    </tr>
    </thead>
    <?php foreach ($invoices as $invoicer) { ?>
      <tr>
        <td><?php echo $invoicer['date']; ?></td>
        <td><?php echo $invoicer['total']; ?></td>
      </tr>
    <?php } ?>
  </table>
<?php
  }
  else {
    echo 'Deze werkbon heeft geen factuur.';
  }
  ?>

</div>

<h2>Betaling</h2>
<div id="workorder-payments">
  <?php
// get the payment for this workorder
  $paymentsr = array();
if ($invoice) {
  $c->clear();
  $c->add(PaymentPeer::INVOICE_ID, $invoice->getId());
  $payments = PaymentPeer::doSelect($c);
  if ($payments) {
    foreach ($payments as $payment) {
      $paymentsr[] = array(
        'status' => $payment->getStatus(),
        'date' => date('d-m-Y', strtotime($invoice->getDate())),
        'paymethod' => $payment->getPaymethodStr(),
        'paymethodv' => $payment->getPaymethod(),
        'total' => '€ ' . number_format($payment->getTotal(), 2, ',', '.'),
        'totalv' => $payment->getTotal(),
        'id' => $payment->getId()
      );
    }
  }
}
  if (count($paymentsr) > 0) { ?>
  <table>
    <thead>
    <tr>
      <th style="width:220px;">Datum</th>
      <th style="width:220px;">Totaal</th>
      <th>Betaalmethode</th>
    </tr>
    </thead>
    <?php foreach ($paymentsr as $payment) { ?>
    <tr>
      <td><?php echo $payment['date']; ?></td>
      <td><?php echo $payment['total']; ?></td>
      <td><?php echo $payment['paymethod']; ?></td>
    </tr>
  <?php } ?>
  </table>
  <?php
  } else {
    echo 'Deze werkbon heeft geen betalingen.';
  }
?>
</div>

<h2>Foto's</h2>
<div id="workorder-photos">
  <?php
  $c = new Criteria;
  $c->add(FilePeer::FTYPE, 'image');
  $c->add(FilePeer::WORKORDER_ID, $workorder->getId());
  $images = FilePeer::doSelect($c);
  $photos = array();
  foreach ($images as $image) {
    $photos[] = array(
      'thumb' => zeusImages::getPresentation($image->getPath(), array('width' => 160, 'height' => 100, 'resize_method' => zeusImages::RESIZE_CHOP))
    );
  }
  if (count($photos) > 0) { ?>
  <ul>
    <?php foreach($photos as $photo) { ?>
    <li><img src="<?php echo $photo['thumb']; ?>"></li>
    <?php } ?>
  </ul>
  <?php
  } else {
    echo "Deze werkbon heeft geen foto's";
  }
  ?>

</div>
<script type="text/javascript">
  window.print();
</script>