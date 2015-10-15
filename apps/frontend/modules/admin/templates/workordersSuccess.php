<script type="text/javascript">
  var workorder_data_url = '<?php echo url_for('admin/workordersData'); ?>';
  var workorder_ajax_url = '<?php echo url_for('admin/workordersAjax'); ?>';
  <?php if ($sf_params->has('detail')) { ?>
  Event.observe(window, 'load', function() {
    for (i in workorder_list.data) {
      if (workorder_list.data.hasOwnProperty(i)) {
        if(workorder_list.data[i][0] == <?php echo $sf_params->get('detail'); ?>) {
          workorder.view(i, workorder_list.data[i]);
        }
      }
    }
  });
  <?php } ?>
  Event.observe(document, 'dom:loaded', function() {
    new MY.DatePicker({
      //embedded: true,
      //embeddedId: 'list-date-filter',
      input: 'list-date-filter',
      format: 'dd-MM-yyyy',
      showWeek: true,
      numberOfMonths: 1
      /*onchange: function() {

      }*/
    });

    Event.observe($('list-date-filter'), 'change', function() {
      workorder_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'keyup', function() {
      workorder_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'blur', function() {
      workorder_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'focus', function() {
      workorder_list.filterDate(this.value, 3);
    });
  });
</script>
<div id="admin-content">
  <h1>Werkbonnen  <a href="#" id="workorder-add-link"><span class="fa fa-edit" title="Werkbon toevoegen"></span></a></h1>
  <!-- list view -->
  <input id="list-date-filter">

  <div id="workorder-list"></div>
  <div id="date-picker" style="width:9em; height: 10em">
</div>
<!-- detail view -->
<div id="workorder-view" class="detail-view" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <h2>Werkbon details <a href="#" id="workorder-edit-link"><span class="fa fa-edit"></span></a></h2>
    <table>
      <tr>
        <td style="width: 220px;font-weight: bold;">Status</td>
        <td id="workorder-view-status"></td>
      </tr>
      <tr>
        <td>Datum</td>
        <td id="workorder-view-date"></td>
      </tr>
      <tr>
        <td>Opmerkingen</td>
        <td id="workorder-view-remarks"></td>
      </tr>
      <tr>
        <td>Gereed</td>
        <td id="workorder-view-ready"></td>
      </tr>
      <tr>
        <td>Medewerker</td>
        <td id="workorder-view-resource"></td>
      </tr>
      <tr>
        <td>Handtekening klant</td>
        <td id="workorder-view-signature"></td>
      </tr>
    </table>

    <h2>Klantgegevens <a href="#" id="customer-edit-link"><span class="fa fa-edit"></span></a></h2>
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
    </table>

    <h2>Orderregels <a href="#" id="workorder-add-link"><span class="fa fa-edit" title="Orderregel toevoegen"></span></a></h2>
    <div id="workorder-orderrows"></div>

    <h2>Factuur</h2>
    <div id="workorder-invoices"></div>

    <h2>Betaling <a href="#" id="payment-edit-link"><span class="fa fa-edit"></span></a></h2>
    <div id="workorder-payments"></div>

    <h2>Foto's</h2>
    <div id="workorder-photos"></div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-2">Bewerken</button>
  </div>
</div>
<!-- edit view -->
<div id="workorder-form" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <div class="form-row">
      <div class="form-label"><label for="workorder-status">Status</label></div>
      <select name="workorder-status" id="workorder-status">
        <option value="started">Gestart</option>
        <option value="success">Afgerond</option>
        <option value="cancelled">Geannuleerd</option>
        <option value="scheduled">Ingepland</option>
      </select>
    </div>

    <div class="form-row">
      <div class="form-label"><label for="workorder-resource">Resource</label></div>
      <select name="workorder-resource" id="workorder-resource" style="width:12em;">
        <?php
        $resources = ResourcePeer::doSelect(new Criteria);
        foreach ($resources as $resource) {
          echo '<option value="'.$resource->getId().'">'.$resource->getName().'</option>';
        }
        ?>
      </select>
    </div>

    <div class="form-row">
      <div class="form-label"><label for="workorder-date">Datum</label></div>
      <input type="text" name="workorder-date" id="workorder-date" style="width:6em;">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="workorder-remarks">Opmerkingen</label></div>
      <textarea cols="40" rows="6" style="width:24em;height:6em;" name="workorder-remarks" id="workorder-remarks"></textarea>
    </div>
    <div class="form-row">
      <div class="form-label"><label for="workorder-ready">Gereed</label></div>
      <input type="checkbox" class="checkbox" name="workorder-ready" id="workorder-ready">
    </div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
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
      € <input type="text" name="orderrow-price" id="orderrow-price" style="width:6em;">
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
  <!-- micro edit for payments -->
  <div id="microedit-payment" style="display: none;">
    <div style="padding:10px;">
      <div class="form-row">
        <div class="form-label"><label for="payment-date">Datum</label></div>
        <input type="text" name="payment-date" id="payment-date" style="width:6em;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="payment-total">Bedrag</label></div>
        € <input type="text" name="payment-total" id="payment-total" style="width:6em;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="payment-type">Betaalmethode</label></div>
        <select type="text" name="payment-paymethod" id="payment-paymethod" style="width:10em;">
          <option value="pin">Pin-betaling</option>
          <option value="cash">Contant</option>
          <option value="invoice">Op rekening</option>
        </select>
      </div>
    </div>
    <div class="form-buttons">
      <button class="button-1">Annuleer</button>
      <button class="button-2">OK</button>
    </div>
  </div>
