<script type="text/javascript">
  var planboard_data_url = '<?php echo url_for('admin/planboardData'); ?>';
  var planboard_ajax_url = '<?php echo url_for('admin/planboardAjax'); ?>';
</script>
<?php
$resources = array();
foreach ($teams as $team) {
  $tmp = array();
  if (!$team) continue;
  if (!isset($team_resources[$team->getId()])) continue;
  foreach ($team_resources[$team->getId()] as $resource) {
    $tmp[] = $resource->getName();
  }
  $resources[$team->getTitle()] = $tmp;
}
if (count($resources) == 0) { ?>
  <h1>Planbord</h1>
  <p>Je hebt nog geen medewerkers aangemaakt.</p>
  <?php
}
else {
?>
<h1>Planbord  <a href="#" id="appointment-add-link"><span class="fa fa-edit" title="Afspraak toevoegen"></span></a></h1>
<div id="filters">
  <div id="filter-date">
    <ul class="pager planboard-pager">
      <li onclick="Planboard.listView();"><a href="#list"><span id="marker-list" class="fa fa-align-justify" title="Lijstweergave"></span></a></li>
      <li onclick="Planboard.gridView();"><a href="#grid"><span id="marker-grid" class="fa fa-th-list" title="Strokenplanner"></span></a></li>
      <li onclick="Planboard.mapView();"><a href="#map"><span id="marker-map" class="fa fa-map-marker active-marker" title="Strokenplanner met kaart"></span></a></li>
      <li class="divider"></li>
      <li id="day-prev" title="Ga een dag terug"><span class="fa fa-angle-left"></span></li>
      <li class="planboard-date"><input id="list-date-filter" style="width:7.2em;" value="<?php echo date('d-m-Y'); ?>"></li>
      <li id="day-next" title="Ga een dag verder" style="position:relative;left:-1em;"><span class="fa fa-angle-right"></span></li>
    </ul>
    <div style="clear:both;"></div>
  </div>
</div>
<!-- list view -->
<div id="dropzone">Sleep een afspraak hierheen om deze van de planning te halen</div>
<div id="view-map">
  <div id="map"></div>
  <div id="planboard">
    <div id="legenda">
      <ul>
        <li>&nbsp;8:00<div></div></li>
        <li>&nbsp;9:00<div></div></li>
        <li>&nbsp;10:00<div></div></li>
        <li>&nbsp;11:00<div></div></li>
        <li>&nbsp;12:00<div></div></li>
        <li>&nbsp;13:00<div></div></li>
        <li>&nbsp;14:00<div></div></li>
        <li>&nbsp;15:00<div></div></li>
        <li>&nbsp;16:00<div></div></li>
        <li>&nbsp;17:00<div></div></li>
        <li>&nbsp;18:00<div></div></li>
        <li>&nbsp;19:00<div></div></li>
        <li>&nbsp;20:00<div></div></li>
      </ul>
    </div>
    <div style="clear:both;"></div>
    <div id="resources">
      <ul>
        <?php foreach ($resources as $team => $res) { ?>
          <li><?php echo $team; ?>
            <ul>
              <?php foreach ($res as $k => $resource_name) { ?>
                <li id="resource-<?php echo $k; ?>"><?php echo $resource_name; ?></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
    </div>
    <div id="planning">
      <ul>
        <?php
        $t = 0;
        foreach ($resources as $team => $res) {
          $t++; ?>
          <li><div<?php if ($t > 1) echo ' class="team-row"'; ?>>&nbsp;</div>
            <ul>
              <?php foreach ($res as $r => $resource_name) { ?>
                <li class="resource_<?php echo $t; ?>_<?php echo $r + 1; ?>" id="resource_<?php echo $t; ?>_<?php echo $r + 1; ?>"></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<div id="view-list" style="display: none;">
  <div id="appointment-list"></div>
</div>
<!-- edit view -->
<div id="appointment-form" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <div style="width:28em;float:left;">
    <h2>Klantgegevens</h2>
    <input type="hidden" name="appointment-customer-id" id="appointment-customer-id" value="0">
    <div class="form-row">
      <div class="form-label"><label for="appointment-ctitle">Klant</label></div>
      <input type="text" name="appointment-ctitle" id="appointment-ctitle">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-address">Adres</label></div>
      <input type="text" name="appointment-address" id="appointment-address">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-zipcode">Postcode</label></div>
      <input type="text" name="appointment-zipcode" id="appointment-zipcode" style="width: 7em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-city">Plaats</label></div>
      <input type="text" name="appointment-city" id="appointment-city" style="width: 12em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-email">E-mail adres</label></div>
      <input type="text" name="appointment-email" id="appointment-email">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-phone">Telefoon</label></div>
      <input type="text" name="appointment-phone" id="appointment-phone" style="width: 7em;">
    </div>
      <?php
      $c = new Criteria;
      $c->add(FieldPeer::COMPANY_ID, $company->getId());
      $c->add(FieldPeer::FORM, 'customer');
      $extra_fields = FieldPeer::doSelect($c);
      foreach ($extra_fields as $extra_field) { ?>
        <div class="form-row">
          <div class="form-label"><label for="appointment-extra-1-<?php echo $extra_field->getId(); ?>"><?php echo $extra_field->getLabel(); ?></label></div>
          <input type="text" class="extra-field" name="appointment-extra-1-<?php echo $extra_field->getId(); ?>" id="appointment-extra-1-<?php echo $extra_field->getId(); ?>">
        </div>
      <?php } ?>
    </div>
    <div style="float:left;">
    <h2>Afspraak</h2>
    <div class="form-row">
      <div class="form-label"><label for="appointment-title">Omschrijving</label></div>
      <input type="text" name="appointment-title" id="appointment-title">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-date">Datum</label></div>
      <input type="text" name="appointment-date" id="appointment-date" style="width:6em;" value="<?php echo date('d-m-Y'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-time-from">Tijd</label></div>
      <input type="text" name="appointment-time-from" id="appointment-time-from" style="width:4em;" value="9:00"> tot <input type="text" name="appointment-time-till" id="appointment-time-till" style="width:4em;" value="10:00">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-resource">Medewerker</label></div>
      <select name="appointment-resource" id="appointment-resource" style="width:12em;">
        <?php
        $resources = ResourcePeer::doSelect(new Criteria);
        foreach ($resources as $resource) {
          echo '<option value="'.$resource->getId().'">'.$resource->getName().'</option>';
        }
        ?>
      </select>
    </div>
    <div class="form-row">
      <div class="form-label"><label for="appointment-color">Label</label></div>
      <input type="hidden" name="appointment-color" id="appointment-color" value="1">
      <div class="color" id="color-1"></div>
      <div class="color active-color" id="color-2"></div>
      <div class="color" id="color-3"></div>
      <div class="color" id="color-4"></div>
      <div class="color" id="color-5"></div>
      <div class="color" id="color-6"></div>
    </div>


      <?php
      $c->clear();
      $c->add(FieldPeer::COMPANY_ID, $company->getId());
      $c->add(FieldPeer::FORM, 'app');
      $extra_fields = FieldPeer::doSelect($c);
      foreach ($extra_fields as $extra_field) { ?>
        <div class="form-row">
          <div class="form-label"><label for="appointment-extra-2-<?php echo $extra_field->getId(); ?>"><?php echo $extra_field->getLabel(); ?></label></div>
          <input type="text" class="extra-field" name="appointment-extra-2-<?php echo $extra_field->getId(); ?>" id="appointment-extra-2-<?php echo $extra_field->getId(); ?>">
        </div>
      <?php } ?>
      <div class="form-row">
        <div class="form-label"><label for="appointment-remarks">Opmerkingen</label></div>
        <textarea cols="40" rows="6" style="width:16em;height:6em;" name="appointment-remarks" id="appointment-remarks"></textarea>
      </div>

    </div>
    <div style="clear:both;"></div>
    <div id="appointment-orderrows-title">
    <h2>Orderregels <a href="#" id="workorder-add-link"><span class="fa fa-edit" title="Orderregel toevoegen"></span></a></h2>
    <div id="appointment-orderrows"></div>
    </div>

    <?php
    $c->clear();
    $c->add(ChecklistPeer::COMPANY_ID, $company->getId());
    $c->addDescendingOrderByColumn(ChecklistPeer::TITLE);
    $checklists = ChecklistPeer::doSelect($c);
    if (count($checklists) > 0) { ?>
      <h2>Gekoppelde controlelijsten</h2>
      <?php
    foreach ($checklists as $checklist) { ?>
      <input type="checkbox" class="checkbox appointment-checklist" name="checklist-<?php echo $checklist->getId(); ?>" id="checklist-<?php echo $checklist->getId(); ?>"> <label for="checklist-<?php echo $checklist->getId(); ?>"><?php echo $checklist->getTitle(); ?></label>
    <?php } } ?>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-3" id="delete-app-btn">Afspraak verwijderen</button>
    <button class="button-2">Opslaan</button>
  </div>
</div>
<!-- micro edit for orderrows -->
<div id="microedit-orderrow" style="display: none;">
  <div style="padding:10px;">
    <div class="form-row">
      <div class="form-label"><label for="orderrow-description">Omschrijving</label></div>
      <input type="text" name="orderrow-description" id="orderrow-description">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="orderrow-type">Type</label></div>
      <select type="text" name="orderrow-type" id="orderrow-type">
        <option value="service">Dienst</option>
        <option value="product">Product</option>
        <option value="hours">Arbeidstijd</option>
      </select>
    </div>
    <div class="form-row" id="orderrow-price-container">
      <div class="form-label"><label for="orderrow-price">Prijs</label></div>
      <input type="text" name="orderrow-price" id="orderrow-price" class="currency">
    </div>
    <div class="form-row" id="orderrow-duration-container">
      <div class="form-label"><label for="orderrow-duration">Tijdsduur ( minuten )</label></div>
      <input type="text" name="orderrow-duration" id="orderrow-duration" style="width:4em;">
    </div>
    <div class="form-row" id="orderrow-amount-container">
      <div class="form-label"><label for="orderrow-amount">Aantal</label></div>
      <input type="text" name="orderrow-amount" id="orderrow-amount" style="width:4em;">
    </div>

  </div>
  <div class="form-buttons">
    <button class="button-1">Annuleer</button>
    <button class="button-2">OK</button>
  </div>
</div>
<script type="text/javascript">
  <?php

  $data = array();
//(startTime, endTime, title, customer, team, resource, longitude, latitude)
foreach ($appointments as $appointment) {
$address = $appointment->getAddress();
if ($address && !$address->getLongitude()) {
  $address_str = $address->getAddress().' '.$address->getZipcode().' '.$address->getCity().' Nederland';
  $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address_str)."&key=AIzaSyDtav4GVB3sPVn0jEPjGfUd7LQ6N56DJPQ");
  if ($json) {
    $location = json_decode(($json));
    if ($location->results) {
      $address->setLongitude($location->results[0]->geometry->location->lng);
      $address->setLatitude($location->results[0]->geometry->location->lat);
    }
    else {
      $address->setLongitude($company->getAddress()->getLongitude());
      $address->setLatitude($company->getAddress()->getLatitude());
    }
    $address->save();
  }
}

if ($address) {
  $longitude = $address->getLongitude(); //'4.4532838';
  $latitude = $address->getLatitude(); //'52.1480517';
}
else {
  $longitude = $company->getAddress()->getLongitude();
  $latitude = $company->getAddress()->getLatitude();
}
  $data[date('d-m-Y', strtotime($appointment->getDate()))][] = array(
    'start' => date('H:i', strtotime($appointment->getDate())),
    'finish' => date('H:i', strtotime($appointment->getEnddate())),
    'duration' => (strtotime($appointment->getEnddate()) - strtotime($appointment->getDate())) / 60,
    'title' => $appointment->getTitle(),
    'customer' => $appointment->getCustomer() ? $appointment->getCustomer()->getTitle() : '',
    'team' => 1,
    'resource' => $appointment->getResourceId(),
    'longitude' => $longitude,
    'latitude' => $latitude,
    'customer_id' => $appointment->getCustomerId(),
    'address_id' => $appointment->getAddressId(),
    'id' => $appointment->getId(),
    'color' => $appointment->getColor()
  );
}

?>
  var appointments = <?php echo json_encode($data); ?>;
  var resource_map = <?php echo json_encode($resource_map); ?>;

  now = Date.now() / 1000 | 0;
  localStorage.setItem('list_data_planboard', Object.toJSON(appointments));
  localStorage.setItem('expire_planboard', now);

  var companyInfo = {
    name: '<?php echo $company->getTitle(); ?>',//'Rijnstreek Verwarming B.V.',
    longitude:  <?php echo $company->getAddress()->getLongitude(); ?>,
    latitude:  <?php echo $company->getAddress()->getLatitude(); ?>
  }

  Event.observe(document, 'dom:loaded', function() {
    new MY.DatePicker({
      //embedded: true,
      //embeddedId: 'list-date-filter',
      input: 'list-date-filter',
      format: 'dd-MM-yyyy',
      showWeek: true,
      numberOfMonths: 1,
      onchange: function() {
        Planboard.plotAppointments(this.value);
        appointment_list.filterDate(this.value);
      }
    });
  });
  Event.observe(window, 'load', function() {
    Event.observe($('list-date-filter'), 'change', function() {
      Planboard.plotAppointments(this.value);
      appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'keyup', function() {
      Planboard.plotAppointments(this.value);
      appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'blur', function() {
      Planboard.plotAppointments(this.value);
      appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'focus', function() {
      Planboard.plotAppointments(this.value);
      appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('day-next'), 'click', function() {
      d = $('list-date-filter').value;
      parts = d.split('-');
      dd = new Date(parts[2]+'-'+parts[1]+'-'+parts[0]);
      dd.setDate(dd.getDate() + 1);
      d = dd.getDate()+'-'+(dd.getMonth()+1)+'-'+dd.getFullYear();
      $('list-date-filter').value = d;
      Planboard.plotAppointments(d);
      appointment_list.filterDate(d, 3);

    });
    Event.observe($('day-prev'), 'click', function() {
      d = $('list-date-filter').value;
      parts = d.split('-');
      dd = new Date(parts[2]+'-'+parts[1]+'-'+parts[0]);
      dd.setDate(dd.getDate() - 1);
      d = dd.getDate()+'-'+(dd.getMonth()+1)+'-'+dd.getFullYear();
      $('list-date-filter').value = d;
      Planboard.plotAppointments(d);
      appointment_list.filterDate(d, 3);
    });
    var anchors = {
      list: 'Planboard.listView();',
      grid: 'Planboard.gridView();',
      map: 'Planboard.mapView();'
    }
    var hash = window.location.hash.substring(1);
    if(anchors[hash]) {
      eval(anchors[hash]);
    }
  });
  <?php if ($sf_params->has('customer')) { ?>
  from_customer_id = <?php echo $sf_params->get('customer'); ?>;
  <?php } ?>
  <?php if ($sf_params->has('method')) { ?>
  start_method = '<?php echo $sf_params->get('method'); ?>';
  <?php } ?>
</script>
<?php } ?>