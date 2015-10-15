<script type="text/javascript">
  var invoice_data_url = '<?php echo url_for('admin/adminData'); ?>';
  var invoice_ajax_url = '<?php echo url_for('admin/adminAjax'); ?>';
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
      invoice_list.filterDate(this.value, 1);
    });
    Event.observe($('list-date-filter'), 'keyup', function() {
      invoice_list.filterDate(this.value, 1);
    });
    Event.observe($('list-date-filter'), 'blur', function() {
      invoice_list.filterDate(this.value, 1);
    });
    Event.observe($('list-date-filter'), 'focus', function() {
      invoice_list.filterDate(this.value, 1);
    });
  });
</script>
<div id="admin-content">
  <h1>Facturen</h1>
<!-- list view -->
  <input id="list-date-filter">
  <div id="invoice-list"></div>
</div>
<!-- detail view -->
<div id="invoice-view" class="detail-view" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <h2>Factuur details <a href="#" id="invoice-edit-link"><span class="fa fa-edit"></span></a></h2>
    <table>
      <tr>
        <td style="width: 220px;font-weight: bold;">Status</td>
        <td id="invoice-view-status"></td>
      </tr>
      <tr>
        <td>Factuurdatum</td>
        <td id="invoice-view-date"></td>
      </tr>
      <tr>
        <td>Factuurnummer</td>
        <td id="invoice-view-no"></td>
      </tr>
      <tr>
        <td>Totaalbedrag</td>
        <td id="invoice-view-total"></td>
      </tr>
    </table>
    <h2>Klantgegevens</h2>
    <table>
      <tr>
        <td style="width: 220px;font-weight: bold;">Naam</td>
        <td id="invoice-view-title"></td>
      </tr>
      <tr>
        <td>Adres</td>
        <td id="invoice-view-address"></td>
      </tr>
      <tr>
        <td>Postcode & plaats</td>
        <td><span id="invoice-view-zipcode"></span> <span id="invoice-view-city"></span></td>
      </tr>
    </table>
    <h2>Orderregels</h2>
    <div id="invoice-orderrows"></div>
    <h2>Betaling <a href="#" id="payment-add-link"><span class="fa fa-edit" title="Betaling toevoegen"></span></a></h2>
    <div id="invoice-payments"></div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <!--<button class="button-2">Bewerken</button>-->
  </div>
</div>
<!-- edit view -->
<div id="invoice-form" style="display:none;">
  <div style="margin: 10px;overflow:auto;">
    <div class="form-row">
      <div class="form-label"><label for="invoice-status">Status</label></div>
      <select name="invoice-status" id="invoice-status">
        <option value="success">Betaald</option>
        <option value="pending">Openstaand</option>
        <option value="service">Service</option>
        <option value="credit">Gecrediteerd</option>
      </select>
    </div>
    <h2>Betaling <a href="#" id="payment-add-link"><span class="fa fa-edit" title="Betaling toevoegen"></span></a></h2>
    <div id="invoice-payments"></div>
  </div>
  <div class="form-buttons">
    <button class="button-1">Sluiten</button>
    <button class="button-2">Opslaan</button>
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
