<script type="text/javascript">
var customer_data_url = '<?php echo url_for('admin/customersData'); ?>';
var customer_ajax_url = '<?php echo url_for('admin/customersAjax'); ?>';
<?php if ($sf_params->has('detail')) { ?>
Event.observe(window, 'load', function() {
  for (i in customer_list.data) {
    if (customer_list.data.hasOwnProperty(i)) {
      if(customer_list.data[i][0] == <?php echo $sf_params->get('detail'); ?>) {
        Customer.view(i, customer_list.data[i]);
      }
    }
  }
});
<?php } ?>
</script>
<div id="admin-content">
  <h1>Klanten  <a href="#" id="customer-add-link"><span class="fa fa-edit" title="Klant toevoegen"></span></a></h1>
<!--
  <div style="border:#cecece 1px solid;padding: 1em;">
    <h2>auto-complete test</h2>
    <input type="text" name="search-test" id="search-test">
    <script type="text/javascript">
      Event.observe(window, 'load', function() {
        var ac = new AutoComplete($('search-test'), { data_url: '<?php echo url_for('admin/customersData?form=search&method=customer'); ?>'});
      });
      </script>
  </div>
  <div>
    <input type="text" name="title" id="title">
    <input type="text" name="zipcode" id="zipcode">
    <input type="text" name="address" id="address">
    <input type="text" name="city" id="city">
  </div>
-->
<!-- list view -->
  <div id="customer-list"></div>
</div>
<!-- detail view -->
<div id="customer-view" class="detail-view" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <h2>Klantgegevens <a href="#" id="customer-edit-link" title="Klantgegevens bewerken"><span class="fa fa-edit"></span></a></h2>
    <table>
      <tr>
        <td style="width: 220px;font-weight: bold;">Naam</td>
        <td id="customer-view-title"></td>
      </tr>
      <tr>
        <td>Adres</td>
        <td id="customer-view-address"></td>
      </tr>
      <tr>
        <td>Postcode & plaats</td>
        <td><span id="customer-view-zipcode"></span> <span id="customer-view-city"></span></td>
      </tr>
      <tr>
        <td>E-mail adres</td>
        <td id="customer-view-email"></td>
      </tr>
      <tr>
        <td>Telefoonnummer</td>
        <td id="customer-view-phone"></td>
      </tr>
      <?php
      $c = new Criteria;
      $c->add(FieldPeer::COMPANY_ID, $company->getId());
      $c->add(FieldPeer::FORM, 'customer');
      $extra_fields = FieldPeer::doSelect($c);
      foreach ($extra_fields as $extra_field) { ?>
        <tr>
          <td><?php echo $extra_field->getLabel(); ?></td>
          <td id="customer-view-extra-1-<?php echo $extra_field->getId(); ?>"></td>
        </tr>
      <?php } ?>
    </table>
    <h2>Werkbonnen <a href="#" id="customer-add-workorder" title="Werkbon toevoegen voor deze klant"><span class="fa fa-edit"></span></a></h2>
    <div id="customer-workorders"></div>
    <h2>Facturen</h2>
    <div id="customer-invoices"></div>
    <h2>Notities <a href="#" id="customer-add-note" title="Notitie toevoegen"><span class="fa fa-edit"></span></a></h2>
    <div id="customer-notes"></div>
    <h2>Foto's</h2>
    <div id="customer-photos"></div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-2">Bewerken</button>
  </div>
</div>
<!-- edit view -->
<div id="customer-form" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <div class="form-row">
      <div class="form-label"><label for="customer-title">Naam</label></div>
      <input type="text" name="customer-title" id="customer-title">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="customer-address">Adres</label></div>
      <input type="text" name="customer-address" id="customer-address">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="customer-zipcode">Postcode</label></div>
      <input type="text" name="customer-zipcode" id="customer-zipcode" style="width: 5em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="customer-city">Plaats</label></div>
      <input type="text" name="customer-city" id="customer-city" style="width: 12em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="customer-email">E-mail adres</label></div>
      <input type="text" name="customer-email" id="customer-email">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="customer-phone">Telefoon</label></div>
      <input type="text" name="customer-phone" id="customer-phone" style="width: 7em;">
    </div>
    <?php
    $c = new Criteria;
    $c->add(FieldPeer::COMPANY_ID, $company->getId());
    $c->add(FieldPeer::FORM, 'customer');
    $extra_fields = FieldPeer::doSelect($c);
    foreach ($extra_fields as $extra_field) { ?>
      <div class="form-row">
        <div class="form-label"><label for="customer-extra-1-<?php echo $extra_field->getId(); ?>"><?php echo $extra_field->getLabel(); ?></label></div>
        <input type="text" class="extra-field" name="customer-extra-1-<?php echo $extra_field->getId(); ?>" id="customer-extra-1-<?php echo $extra_field->getId(); ?>">
      </div>
    <?php } ?>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-2">Opslaan</button>
  </div>
</div>
<!-- micro edit for notes -->
<div id="microedit-note" style="display: none;">
  <div style="padding:10px;">
    <div class="form-row">
      <div class="form-label"><label for="note-date">Datum</label></div>
      <input type="text" name="note-date" id="note-date" style="width:6em;" value="<?php echo date('d-m-Y'); ?>">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="note-text">Notitie</label></div>
      <textarea name="note-text" id="note-text" style="width:30em;height:12em;"></textarea>
    </div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Annuleer</button>
    <button class="button-2">OK</button>
  </div>
</div>