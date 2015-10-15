<script type="text/javascript">
  var planboard_data_url = '<?php echo url_for('admin/planboardData'); ?>';
  var planboard_ajax_url = '<?php echo url_for('admin/planboardAjax'); ?>';
</script>
<?php
foreach ($teams as $team) {
  $tmp = array();
  if (!$team) continue;
  if (!isset($team_resources[$team->getId()])) continue;
  foreach ($team_resources[$team->getId()] as $resource) {
    $tmp[] = $resource->getName();
  }
  $resources[$team->getTitle()] = $tmp;
}
/*
$resources = array(
    'Team 1' => array(
      'Ricardo', 'Jean-Paul', 'Leon'
    ),
    'Team 2' => array(
      'Jorgen', 'Hans'
    )
  );
*/
?>
<!--<h1>Planbord  <a href="#" id="appointment-add-link"><span class="fa fa-edit" title="Afspraak toevoegen"></span></a></h1>-->
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
              <?php foreach ($res as $resource_name) { ?>
                <li><?php echo $resource_name; ?></li>
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
      <div class="form-label"><label for="appointment-color">Kleurcode</label></div>
      <select name="appointment-color" id="appointment-color" style="width:12em;">
        <option value="1" style="background: #c00;"><span style="display:block;background: #c00; height: 1em;">&nbsp;</span></option>
      </select>
    </div>


    <div class="form-row">
      <div class="form-label"><label for="appointment-remarks">Opmerkingen</label></div>
      <textarea cols="40" rows="6" style="width:18em;height:6em;" name="appointment-remarks" id="appointment-remarks"></textarea>
    </div>
    </div>
    <div style="clear:both;"></div>
    <h2>Orderregels <a href="#" id="workorder-add-link"><span class="fa fa-edit" title="Orderregel toevoegen"></span></a></h2>
    <div id="appointment-orderrows"></div>

  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-2">Opslaan</button>
  </div>
</div>
<script type="text/javascript">
  <?php
//(startTime, endTime, title, customer, team, resource, longitude, latitude)
foreach ($appointments as $appointment) {
  $data[date('d-m-Y', strtotime($appointment->getDate()))][] = array(
    'start' => date('H:i', strtotime($appointment->getDate())),
    'finish' => date('H:i', strtotime($appointment->getEnddate())),
    'duration' => (strtotime($appointment->getEnddate()) - strtotime($appointment->getDate())) / 60,
    'title' => $appointment->getTitle(),
    'customer' => $appointment->getCustomer() ? $appointment->getCustomer()->getTitle() : '',
    'team' => 1,
    'resource' => $appointment->getResourceId(),
    'longitude' => '4.4532838',
    'latitude' => '52.1480517',
    'customer_id' => $appointment->getCustomerId(),
    'address_id' => $appointment->getAddressId(),
    'id' => $appointment->getId(),
    'color' => $appointment->getColor()
  );
}
?>
  var appointments = <?php echo json_encode($data); ?>;
  var resource_map = <?php echo json_encode($resource_map); ?>;

  Event.observe(document, 'dom:loaded', function() {
    new MY.DatePicker({
      embedded: true,
      embeddedId: 'date-picker',
      format: 'dd-MM-yyyy',
      showWeek: true,
      numberOfMonths: 1,
      onchange: function() {
        Planboard.plotAppointments(this.value);
        appointment_list.filterDate(this.value);
      }
    });
  });
</script>